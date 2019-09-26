<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Test;

use webignition\BasilContextAwareException\ContextAwareExceptionInterface;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilDataStructure\PathResolver;
use webignition\BasilDataStructure\Step as StepData;
use webignition\BasilDataStructure\Test\Configuration as ConfigurationData;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\DataSet\DataSet;
use webignition\BasilModel\DataSet\DataSetCollection;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\IdentifierCollection;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Step\PendingImportResolutionStep;
use webignition\BasilModel\Step\Step;
use webignition\BasilModel\Test\Configuration;
use webignition\BasilModel\Test\Test;
use webignition\BasilModel\Test\TestInterface;
use webignition\BasilDataStructure\Test\Test as TestData;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
use webignition\BasilModelFactory\Test\TestFactory;

class TestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TestFactory
     */
    private $testFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testFactory = TestFactory::createFactory();
    }

    /**
     * @dataProvider createFromTestDataDataProvider
     */
    public function testCreateFromTestDataSuccess(string $name, TestData $testData, TestInterface $expectedTest)
    {
        $test = $this->testFactory->createFromTestData($name, $testData);

        $this->assertEquals($expectedTest, $test);
    }

    public function createFromTestDataDataProvider(): array
    {
        $configurationData = [
            ConfigurationData::KEY_BROWSER => 'chrome',
            ConfigurationData::KEY_URL => 'http://example.com',
        ];

        $expectedConfiguration = new Configuration('chrome', 'http://example.com');

        return [
            'empty' => [
                'name' => '',
                'testData' => new TestData(PathResolver::create(), []),
                'expectedTest' => new Test(
                    '',
                    new Configuration('', ''),
                    []
                ),
            ],
            'configuration only' => [
                'name' => 'configuration only',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                    ]
                ),
                'expectedTest' => new Test('configuration only', $expectedConfiguration, []),
            ],
            'invalid inline steps only' => [
                'name' => 'invalid inline steps only',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'invalid' => [
                            StepData::KEY_ACTIONS => true,
                            StepData::KEY_ASSERTIONS => [
                                '',
                                false,
                            ],
                        ],
                    ]
                ),
                'expectedTest' => new Test('invalid inline steps only', $expectedConfiguration, [
                    'invalid' => new Step([], []),
                ]),
            ],
            'inline step, scalar values' => [
                'name' => 'inline step, scalar values',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'verify page is open' => [
                            StepData::KEY_ASSERTIONS => [
                                '$page.url is "http://example.com"',
                            ],
                        ],
                        'query "example"' => [
                            StepData::KEY_ACTIONS => [
                                'click ".form .submit"',
                            ],
                            StepData::KEY_ASSERTIONS => [
                                '$page.title is "example - Example Domain"',
                            ],
                        ],
                    ]
                ),
                'expectedTest' => new Test('inline step, scalar values', $expectedConfiguration, [
                    'verify page is open' => new Step([], [
                        new ComparisonAssertion(
                            '$page.url is "http://example.com"',
                            new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url'),
                            AssertionComparison::IS,
                            new LiteralValue('http://example.com')
                        ),
                    ]),
                    'query "example"' => new Step(
                        [
                            new InteractionAction(
                                'click ".form .submit"',
                                ActionTypes::CLICK,
                                new DomIdentifier('.form .submit'),
                                '".form .submit"'
                            ),
                        ],
                        [
                            new ComparisonAssertion(
                                '$page.title is "example - Example Domain"',
                                new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.title', 'title'),
                                AssertionComparison::IS,
                                new LiteralValue('example - Example Domain')
                            ),
                        ]
                    ),
                ]),
            ],
            'inline step, page element references' => [
                'name' => 'inline step, page element references',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'query "example"' => [
                            StepData::KEY_ACTIONS => [
                                'click page_import_name.elements.button',
                            ],
                            StepData::KEY_ASSERTIONS => [
                                'page_import_name.elements.heading is "example"',
                            ],
                        ],
                    ]
                ),
                'expectedTest' => new Test('inline step, page element references', $expectedConfiguration, [
                    'query "example"' => new Step(
                        [
                            new InteractionAction(
                                'click page_import_name.elements.button',
                                ActionTypes::CLICK,
                                ReferenceIdentifier::createPageElementReferenceIdentifier(
                                    new PageElementReference(
                                        'page_import_name.elements.button',
                                        'page_import_name',
                                        'button'
                                    )
                                ),
                                'page_import_name.elements.button'
                            ),
                        ],
                        [
                            new ComparisonAssertion(
                                'page_import_name.elements.heading is "example"',
                                new PageElementReference(
                                    'page_import_name.elements.heading',
                                    'page_import_name',
                                    'heading'
                                ),
                                AssertionComparison::IS,
                                new LiteralValue('example')
                            ),
                        ]
                    ),
                ]),
            ],
            'invalid page import path, unused' => [
                'name' => 'invalid page import path, unused',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                    ]
                ),
                'expectedTest' => new Test('invalid page import path, unused', $expectedConfiguration, []),
            ],
            'invalid step import path, unused' => [
                'name' => 'invalid step import path, unused',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                    ]
                ),
                'expectedTest' => new Test('invalid step import path, unused', $expectedConfiguration, []),
            ],
            'invalid data provider import path, unused' => [
                'name' => 'invalid data provider import path, unused',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                    ]
                ),
                'expectedTest' => new Test('invalid data provider import path, unused', $expectedConfiguration, []),
            ],
            'step import, no parameters' => [
                'name' => 'step import, no parameters',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'step_name' => [
                            StepData::KEY_USE => 'step_import_name',
                        ],
                    ]
                ),
                'expectedTest' => new Test(
                    'step import, no parameters',
                    $expectedConfiguration,
                    [
                        'step_name' => new PendingImportResolutionStep(new Step([], []), 'step_import_name', ''),
                    ]
                ),
            ],
            'step import, inline data' => [
                'name' => 'step import, inline data',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'step_name' => [
                            StepData::KEY_USE => 'step_import_name',
                            StepData::KEY_DATA => [
                                'data_set_1' => [
                                    'expected_title' => 'Foo',
                                ],
                            ]
                        ],
                    ]
                ),
                'expectedTest' => new Test(
                    'step import, inline data',
                    $expectedConfiguration,
                    [
                        'step_name' => (new PendingImportResolutionStep(
                            new Step([], []),
                            'step_import_name',
                            ''
                        ))->withDataSetCollection(new DataSetCollection([
                            'data_set_1' => new DataSet('data_set_1', [
                                'expected_title' => 'Foo',
                            ]),
                        ])),
                    ]
                ),
            ],
            'step import, imported data' => [
                'name' => 'step import, imported data',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'step_name' => [
                            StepData::KEY_USE => 'step_import_name',
                            StepData::KEY_DATA => 'data_provider_import_name',
                        ],
                    ]
                ),
                'expectedTest' => new Test(
                    'step import, imported data',
                    $expectedConfiguration,
                    [
                        'step_name' => new PendingImportResolutionStep(
                            new Step([], []),
                            'step_import_name',
                            'data_provider_import_name'
                        ),
                    ]
                ),
            ],
            'step import, uses page imported page elements' => [
                'name' => 'step import, uses page imported page elements',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => $configurationData,
                        'step_name' => [
                            StepData::KEY_USE => 'step_import_name',
                            StepData::KEY_ELEMENTS => [
                                'heading' => 'page_import_name.elements.heading'
                            ],
                        ],
                    ]
                ),
                'expectedTest' => new Test(
                    'step import, uses page imported page elements',
                    $expectedConfiguration,
                    [
                        'step_name' => (new PendingImportResolutionStep(
                            new Step([], []),
                            'step_import_name',
                            ''
                        ))->withIdentifierCollection(new IdentifierCollection([
                            (ReferenceIdentifier::createPageElementReferenceIdentifier(
                                new PageElementReference(
                                    'page_import_name.elements.heading',
                                    'page_import_name',
                                    'heading'
                                )
                            ))->withName('heading')
                        ])),
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider createFromTestDataThrowsExceptionDataProvider
     */
    public function testCreateFromTestDataThrowsException(
        string $name,
        TestData $testData,
        string $expectedException,
        string $expectedExceptionMessage,
        ExceptionContext $expectedExceptionContext
    ) {
        try {
            $this->testFactory->createFromTestData($name, $testData);
        } catch (ContextAwareExceptionInterface $contextAwareException) {
            $this->assertInstanceOf($expectedException, $contextAwareException);
            $this->assertEquals($expectedExceptionMessage, $contextAwareException->getMessage());
            $this->assertEquals($expectedExceptionContext, $contextAwareException->getExceptionContext());
        }
    }

    public function createFromTestDataThrowsExceptionDataProvider(): array
    {
        return [
            'action string contains invalid identifier string' => [
                'name' => 'test name',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => [
                            ConfigurationData::KEY_BROWSER => 'chrome',
                            ConfigurationData::KEY_URL => 'http://example.com',
                        ],
                        'step name' => [
                            StepData::KEY_ACTIONS => [
                                'click action_one_element_reference',
                            ],
                        ],
                    ]
                ),
                'expectedException' => InvalidIdentifierStringException::class,
                'expectedExceptionMessage' => 'Invalid identifier string "action_one_element_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'click action_one_element_reference',
                ]),
            ],
            'action string contains invalid action type' => [
                'name' => 'test name',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => [
                            ConfigurationData::KEY_BROWSER => 'chrome',
                            ConfigurationData::KEY_URL => 'http://example.com',
                        ],
                        'step name' => [
                            StepData::KEY_ACTIONS => [
                                'foo ".selector"',
                            ],
                        ],
                    ]
                ),
                'expectedException' => InvalidActionTypeException::class,
                'expectedExceptionMessage' => 'Invalid action type "foo"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'foo ".selector"',
                ]),
            ],
            'action string lacks value' => [
                'name' => 'test name',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => [
                            ConfigurationData::KEY_BROWSER => 'chrome',
                            ConfigurationData::KEY_URL => 'http://example.com',
                        ],
                        'step name' => [
                            StepData::KEY_ACTIONS => [
                                'set ".selector" to',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MissingValueException::class,
                'expectedExceptionMessage' => '',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'set ".selector" to',
                ]),
            ],
            'assertion string lacks value' => [
                'name' => 'test name',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => [
                            ConfigurationData::KEY_BROWSER => 'chrome',
                            ConfigurationData::KEY_URL => 'http://example.com',
                        ],
                        'step name' => [
                            StepData::KEY_ASSERTIONS => [
                                '".selector" is',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MissingValueException::class,
                'expectedExceptionMessage' => '',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => '".selector" is',
                ]),
            ],
            'test.elements contains malformed reference' => [
                'name' => 'test name',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => [
                            ConfigurationData::KEY_BROWSER => 'chrome',
                            ConfigurationData::KEY_URL => 'http://example.com',
                        ],
                        'step one' => [
                            StepData::KEY_USE => 'step_import_name',
                            StepData::KEY_ELEMENTS => [
                                'heading' => 'invalid_page_element_reference',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MalformedPageElementReferenceException::class,
                'expectedExceptionMessage' => 'Malformed page element reference "invalid_page_element_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step one',
                ]),
            ],
        ];
    }
}
