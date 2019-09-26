<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

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
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilModelFactory\StepFactory;

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
        return [
            'empty step data' => [
                'stepData' => new StepData([]),
                'expectedStep' => new Step([], []),
            ],
            'empty actions and empty assertions' => [
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        '',
                        ' ',
                    ],
                    StepData::KEY_ASSERTIONS => [
                        '',
                        ' ',
                    ],
                ]),
                'expectedStep' => new Step([], []),
            ],
            'actions only' => [
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        'click ".selector"',
                        'set ".input" to "value"',
                    ],
                ]),
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
                'stepData' => new StepData([
                    StepData::KEY_ASSERTIONS => [
                        '".selector" is "value"',
                        '".input" exists'
                    ],
                ]),
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
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        'click page_import_name.elements.element_name'
                    ],
                    StepData::KEY_ASSERTIONS => [
                        'page_import_name.elements.element_name exists'
                    ],
                ]),
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
                'stepData' => new StepData([
                    StepData::KEY_USE => 'import_name',
                ]),
                'expectedStep' => new PendingImportResolutionStep(new Step([], []), 'import_name', ''),
            ],
            'data provider name only' => [
                'stepData' => new StepData([
                    StepData::KEY_DATA => 'data_provider_import_name',
                ]),
                'expectedStep' => new PendingImportResolutionStep(new Step([], []), '', 'data_provider_import_name'),
            ],
            'import name and data provider name' => [
                'stepData' => new StepData([
                    StepData::KEY_USE => 'import_name',
                    StepData::KEY_DATA => 'data_provider_import_name',
                ]),
                'expectedStep' => new PendingImportResolutionStep(
                    new Step([], []),
                    'import_name',
                    'data_provider_import_name'
                ),
            ],
            'import name and inline data' => [
                'stepData' => new StepData([
                    StepData::KEY_USE => 'import_name',
                    StepData::KEY_DATA => [
                        'data_set_1' => [
                            'expected_title' => 'Foo',
                        ],
                    ]
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
                'stepData' => new StepData([
                    StepData::KEY_USE => 'import_name',
                    StepData::KEY_ELEMENTS => [
                        'heading' => 'page_import_name.elements.heading'
                    ],
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
                'stepData' => new StepData([
                    StepData::KEY_USE => 'import_name',
                    StepData::KEY_DATA => 'data_provider_import_name',
                    StepData::KEY_ACTIONS => [
                        'click ".selector"',
                    ],
                    StepData::KEY_ASSERTIONS => [
                        '".selector" exists',
                    ],
                ]),
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
        return [
            'invalid identifier string within action string' => [
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        'click page.element',
                    ],
                ]),
                'expectedException' => InvalidIdentifierStringException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => 'click page.element',
                ]),
            ],
            'invalid action type' => [
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        'foo ".selector"',
                    ],
                ]),
                'expectedException' => InvalidActionTypeException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => 'foo ".selector"',
                ]),
            ],
            'set action missing value' => [
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        'set ".selector" to',
                    ],
                ]),
                'expectedException' => MissingValueException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => 'set ".selector" to',
                ]),
            ],
            'is assertion missing value' => [
                'stepData' => new StepData([
                    StepData::KEY_ASSERTIONS => [
                        '".selector" is',
                    ],
                ]),
                'expectedException' => MissingValueException::class,
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => '".selector" is',
                ]),
            ],
        ];
    }
}
