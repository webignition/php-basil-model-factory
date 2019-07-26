<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\DataSet\DataSet;
use webignition\BasilModel\DataSet\DataSetCollection;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierCollection;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Step\PendingImportResolutionStep;
use webignition\BasilModel\Step\Step;
use webignition\BasilModel\Step\StepInterface;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilDataStructure\Step as StepData;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
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
                            new ElementIdentifier(
                                LiteralValue::createCssSelectorValue('.selector')
                            ),
                            '".selector"'
                        ),
                        new InputAction(
                            'set ".input" to "value"',
                            new ElementIdentifier(
                                LiteralValue::createCssSelectorValue('.input')
                            ),
                            LiteralValue::createStringValue('value'),
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
                        new Assertion(
                            '".selector" is "value"',
                            new ElementValue(
                                new ElementIdentifier(
                                    LiteralValue::createCssSelectorValue('.selector')
                                )
                            ),
                            AssertionComparisons::IS,
                            LiteralValue::createStringValue('value')
                        ),
                        new Assertion(
                            '".input" exists',
                            new ElementValue(
                                new ElementIdentifier(
                                    LiteralValue::createCssSelectorValue('.input')
                                )
                            ),
                            AssertionComparisons::EXISTS
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
                            new Identifier(
                                IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                                new ObjectValue(
                                    ValueTypes::PAGE_ELEMENT_REFERENCE,
                                    'page_import_name.elements.element_name',
                                    'page_import_name',
                                    'element_name'
                                )
                            ),
                            'page_import_name.elements.element_name'
                        )
                    ],
                    [
                        new Assertion(
                            'page_import_name.elements.element_name exists',
                            new ObjectValue(
                                ValueTypes::PAGE_ELEMENT_REFERENCE,
                                'page_import_name.elements.element_name',
                                'page_import_name',
                                'element_name'
                            ),
                            AssertionComparisons::EXISTS
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
                    (new Identifier(
                        IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                        new ObjectValue(
                            ValueTypes::PAGE_ELEMENT_REFERENCE,
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
                                new ElementIdentifier(
                                    LiteralValue::createCssSelectorValue('.selector')
                                ),
                                '".selector"'
                            ),
                        ],
                        [
                            new Assertion(
                                '".selector" exists',
                                new ElementValue(
                                    new ElementIdentifier(
                                        LiteralValue::createCssSelectorValue('.selector')
                                    )
                                ),
                                AssertionComparisons::EXISTS
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
     * @dataProvider applyContextToMalformedPageElementReferenceExceptionDataProvider
     */
    public function testApplyContextToMalformedPageElementReferenceException(
        StepData $stepData,
        ExceptionContext $expectedExceptionContext
    ) {
        try {
            $this->stepFactory->createFromStepData($stepData);

            $this->fail('MalformedPageElementReferenceException not thrown');
        } catch (MalformedPageElementReferenceException $malformedPageElementReferenceException) {
            $this->assertEquals(
                $expectedExceptionContext,
                $malformedPageElementReferenceException->getExceptionContext()
            );
        }
    }

    public function applyContextToMalformedPageElementReferenceExceptionDataProvider(): array
    {
        return [
            'within action string' => [
                'stepData' => new StepData([
                    StepData::KEY_ACTIONS => [
                        'click page.element',
                    ],
                ]),
                'expectedExceptionContext' => new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => null,
                    ExceptionContextInterface::KEY_STEP_NAME => null,
                    ExceptionContextInterface::KEY_CONTENT => 'click page.element',
                ]),
            ],
        ];
    }
}
