<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilContextAwareException\ContextAwareExceptionInterface;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\DataSet\DataSet;
use webignition\BasilModel\DataSet\DataSetCollection;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\IdentifierCollection;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Step\PendingImportResolutionStep;
use webignition\BasilModel\Step\Step;
use webignition\BasilModel\Step\StepInterface;
use webignition\BasilModel\Value\DomIdentifierValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilDataStructure\Step as StepData;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingComparisonException;
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilModelFactory\StepFactory;
use webignition\BasilParser\ActionParser;
use webignition\BasilParser\AssertionParser;

class StepFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StepFactory
     */
    private $stepFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stepFactory = StepFactory::createFactory();
    }

    /**
     * @dataProvider createFromStepDataDataProvider
     */
    public function testCreateFromStepData(StepData $stepData, StepInterface $expectedStep)
    {
        $step = $this->stepFactory->createFromStepData($stepData);

        $this->assertEquals($expectedStep, $step);
    }

    public function createFromStepDataDataProvider(): array
    {
        $actionParser = ActionParser::create();
        $assertionParser = AssertionParser::create();

        return [
            'empty step data' => [
                'stepData' => new StepData([], []),
                'expectedStep' => new Step([], []),
            ],
            'actions only' => [
                'stepData' => new StepData(
                    [
                        $actionParser->parse('click ".selector"'),
                        $actionParser->parse('set ".input" to "value"'),
                    ],
                    []
                ),
                'expectedStep' => new Step(
                    [
                        new InteractionAction(
                            'click ".selector"',
                            ActionTypes::CLICK,
                            new DomIdentifier('.selector'),
                            '".selector"'
                        ),
                        new InputAction(
                            'set ".input" to "value"',
                            new DomIdentifier('.input'),
                            new LiteralValue('value'),
                            '".input" to "value"'
                        )
                    ],
                    []
                ),
            ],
            'assertions only' => [
                'stepData' => new StepData(
                    [],
                    [
                        $assertionParser->parse('".selector" is "value"'),
                        $assertionParser->parse('".input" exists'),
                    ]
                ),
                'expectedStep' => new Step(
                    [
                    ],
                    [
                        new ComparisonAssertion(
                            '".selector" is "value"',
                            DomIdentifierValue::create('.selector'),
                            AssertionComparison::IS,
                            new LiteralValue('value')
                        ),
                        new ExaminationAssertion(
                            '".input" exists',
                            DomIdentifierValue::create('.input'),
                            AssertionComparison::EXISTS
                        ),
                    ]
                ),
            ],
            'page element references' => [
                'stepData' => new StepData(
                    [
                        $actionParser->parse('click page_import_name.elements.element_name'),
                    ],
                    [
                        $assertionParser->parse('page_import_name.elements.element_name exists'),
                    ]
                ),
                'expectedStep' => new Step(
                    [
                        new InteractionAction(
                            'click page_import_name.elements.element_name',
                            ActionTypes::CLICK,
                            ReferenceIdentifier::createPageElementReferenceIdentifier(
                                new PageElementReference(
                                    'page_import_name.elements.element_name',
                                    'page_import_name',
                                    'element_name'
                                )
                            ),
                            'page_import_name.elements.element_name'
                        )
                    ],
                    [
                        new ExaminationAssertion(
                            'page_import_name.elements.element_name exists',
                            new PageElementReference(
                                'page_import_name.elements.element_name',
                                'page_import_name',
                                'element_name'
                            ),
                            AssertionComparison::EXISTS
                        ),
                    ]
                ),
            ],
            'import name only' => [
                'stepData' => (new StepData([], []))
                    ->withImportName('import_name'),
                'expectedStep' => new PendingImportResolutionStep(new Step([], []), 'import_name', ''),
            ],
            'data provider name only' => [
                'stepData' => (new StepData([], []))
                    ->withDataImportName('data_provider_import_name'),
                'expectedStep' => new PendingImportResolutionStep(new Step([], []), '', 'data_provider_import_name'),
            ],
            'import name and data provider name' => [
                'stepData' => (new StepData([], []))
                    ->withImportName('import_name')
                    ->withDataImportName('data_provider_import_name'),
                'expectedStep' => new PendingImportResolutionStep(
                    new Step([], []),
                    'import_name',
                    'data_provider_import_name'
                ),
            ],
            'import name and inline data' => [
                'stepData' => (new StepData([], []))
                    ->withImportName('import_name')
                    ->withDataArray([
                        'data_set_1' => [
                            'expected_title' => 'Foo',
                        ],
                    ]),
                'expectedStep' => (new PendingImportResolutionStep(
                    new Step([], []),
                    'import_name',
                    ''
                ))->withDataSetCollection(new DataSetCollection([
                    'data_set_1' => new DataSet('data_set_1', [
                        'expected_title' => 'Foo',
                    ]),
                ])),
            ],
            'import name and page imported page elements' => [
                'stepData' => (new StepData([], []))
                    ->withImportName('import_name')
                    ->withElements([
                        'heading' => 'page_import_name.elements.heading'
                    ]),
                'expectedStep' => (new PendingImportResolutionStep(
                    new Step([], []),
                    'import_name',
                    ''
                ))->withIdentifierCollection(new IdentifierCollection([
                    (ReferenceIdentifier::createPageElementReferenceIdentifier(
                        new PageElementReference(
                            'page_import_name.elements.heading',
                            'page_import_name',
                            'heading'
                        )
                    ))->withName('heading'),
                ])),
            ],
            'import name, data provider name, actions and assertions' => [
                'stepData' =>
                    (new StepData(
                        [
                            $actionParser->parse('click ".selector"'),
                        ],
                        [
                            $assertionParser->parse('".selector" exists'),
                        ]
                    ))
                        ->withImportName('import_name')
                        ->withDataImportName('data_provider_import_name'),
                'expectedStep' => new PendingImportResolutionStep(
                    new Step(
                        [
                            new InteractionAction(
                                'click ".selector"',
                                ActionTypes::CLICK,
                                new DomIdentifier('.selector'),
                                '".selector"'
                            ),
                        ],
                        [
                            new ExaminationAssertion(
                                '".selector" exists',
                                DomIdentifierValue::create('.selector'),
                                AssertionComparison::EXISTS
                            ),
                        ]
                    ),
                    'import_name',
                    'data_provider_import_name'
                ),
            ],
        ];
    }

    /**
     * @dataProvider applyContextToContextAwareExceptionDataProvider
     */
    public function testApplyContextToContextAwareException(
        StepData $stepData,
        string $expectedException,
        ExceptionContext $expectedExceptionContext
    ) {
        try {
            $this->stepFactory->createFromStepData($stepData);

            $this->fail('ContextAwareExceptionInterface not thrown');
        } catch (ContextAwareExceptionInterface $contextAwareException) {
            $this->assertInstanceOf($expectedException, $contextAwareException);

            $this->assertEquals(
                $expectedExceptionContext,
                $contextAwareException->getExceptionContext()
            );
        }
    }

    public function applyContextToContextAwareExceptionDataProvider(): array
    {
        $actionParser = ActionParser::create();
        $assertionParser = AssertionParser::create();

        return [
            'invalid identifier string within action string' => [
                'stepData' => new StepData(
                    [
                        $actionParser->parse('click page.element'),
                    ],
                    []
                ),
                'expectedException' => InvalidIdentifierStringException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => 'click page.element',
                ]),
            ],
            'invalid action type' => [
                'stepData' => new StepData(
                    [
                        $actionParser->parse('foo ".selector"'),
                    ],
                    []
                ),
                'expectedException' => InvalidActionTypeException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => 'foo ".selector"',
                ]),
            ],
            'is assertion missing value' => [
                'stepData' => new StepData(
                    [],
                    [
                        $assertionParser->parse('".selector" is'),
                    ]
                ),
                'expectedException' => MissingValueException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => '".selector" is',
                ]),
            ],
            'assertion missing comparison' => [
                'stepData' => new StepData(
                    [],
                    [
                        $assertionParser->parse('".selector" '),
                    ]
                ),
                'expectedException' => MissingComparisonException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => '".selector"',
                ]),
            ],
        ];
    }
}
