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
use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\DataSet\DataSet;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Step\PendingImportResolutionStep;
use webignition\BasilModel\Step\Step;
use webignition\BasilModel\Test\Configuration;
use webignition\BasilModel\Test\Test;
use webignition\BasilModel\Test\TestInterface;
use webignition\BasilDataStructure\Test\Test as TestData;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\Value;
use webignition\BasilModel\Value\ValueTypes;
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
                        new Assertion(
                            '$page.url is "http://example.com"',
                            new Identifier(
                                IdentifierTypes::PAGE_OBJECT_PARAMETER,
                                new ObjectValue(
                                    ValueTypes::PAGE_OBJECT_PROPERTY,
                                    '$page.url',
                                    'page',
                                    'url'
                                )
                            ),
                            AssertionComparisons::IS,
                            new Value(
                                ValueTypes::STRING,
                                'http://example.com'
                            )
                        ),
                    ]),
                    'query "example"' => new Step(
                        [
                            new InteractionAction(
                                'click ".form .submit"',
                                ActionTypes::CLICK,
                                new Identifier(
                                    IdentifierTypes::CSS_SELECTOR,
                                    new Value(
                                        ValueTypes::STRING,
                                        '.form .submit'
                                    )
                                ),
                                '".form .submit"'
                            ),
                        ],
                        [
                            new Assertion(
                                '$page.title is "example - Example Domain"',
                                new Identifier(
                                    IdentifierTypes::PAGE_OBJECT_PARAMETER,
                                    new ObjectValue(
                                        ValueTypes::PAGE_OBJECT_PROPERTY,
                                        '$page.title',
                                        'page',
                                        'title'
                                    )
                                ),
                                AssertionComparisons::IS,
                                new Value(
                                    ValueTypes::STRING,
                                    'example - Example Domain'
                                )
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
                                new Identifier(
                                    IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                                    new Value(
                                        ValueTypes::STRING,
                                        'page_import_name.elements.button'
                                    ),
                                    null
                                ),
                                'page_import_name.elements.button'
                            ),
                        ],
                        [
                            new Assertion(
                                'page_import_name.elements.heading is "example"',
                                new Identifier(
                                    IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                                    new Value(
                                        ValueTypes::STRING,
                                        'page_import_name.elements.heading'
                                    ),
                                    null
                                ),
                                AssertionComparisons::IS,
                                new Value(
                                    ValueTypes::STRING,
                                    'example'
                                )
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
                        ))->withDataSets([
                            'data_set_1' => new DataSet([
                                'expected_title' => 'Foo',
                            ]),
                        ]),
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
                        ))->withElementIdentifiers([
                            'heading' => new Identifier(
                                IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                                new Value(
                                    ValueTypes::STRING,
                                    'page_import_name.elements.heading'
                                ),
                                null,
                                'heading'
                            )
                        ]),
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider createFromTestDataThrowsMalformedPageElementReferenceExceptionDataProvider
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

    public function createFromTestDataThrowsMalformedPageElementReferenceExceptionDataProvider(): array
    {
        // MalformedPageElementReferenceException
        //   thrown when trying to uses a page element reference that is not of the correct form
        //
        //   cases:
        //   - assertion string contains malformed reference
        //   - action string contains malformed reference
        //   - test.elements contains malformed reference

        return [
            'MalformedPageElementReferenceException: assertion string contains malformed reference (1)' => [
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
                                'malformed_reference is "assertion one value"',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MalformedPageElementReferenceException::class,
                'expectedExceptionMessage' => 'Malformed page element reference "malformed_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'malformed_reference is "assertion one value"',
                ]),
            ],
            'MalformedPageElementReferenceException: assertion string contains malformed reference (2)' => [
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
                                '".heading" is "assertion one value"',
                                'malformed_reference is "assertion two value"',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MalformedPageElementReferenceException::class,
                'expectedExceptionMessage' => 'Malformed page element reference "malformed_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'malformed_reference is "assertion two value"',
                ]),
            ],
            'MalformedPageElementReferenceException: action string contains malformed reference (1)' => [
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
                'expectedException' => MalformedPageElementReferenceException::class,
                'expectedExceptionMessage' => 'Malformed page element reference "action_one_element_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'click action_one_element_reference',
                ]),
            ],
            'MalformedPageElementReferenceException: action string contains malformed reference (2)' => [
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
                                'click ".heading"',
                                'click action_two_element_reference',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MalformedPageElementReferenceException::class,
                'expectedExceptionMessage' => 'Malformed page element reference "action_two_element_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step name',
                    ExceptionContextInterface::KEY_CONTENT => 'click action_two_element_reference',
                ]),
            ],
            'MalformedPageElementReferenceException: test.elements contains malformed reference (1)' => [
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
            'MalformedPageElementReferenceException: test.elements contains malformed reference (2)' => [
                'name' => 'test name',
                'testData' => new TestData(
                    PathResolver::create(),
                    [
                        TestData::KEY_CONFIGURATION => [
                            ConfigurationData::KEY_BROWSER => 'chrome',
                            ConfigurationData::KEY_URL => 'http://example.com',
                        ],
                        'step one' => [
                            StepData::KEY_ASSERTIONS => [
                                '$page.url is "http://example.com"',
                            ],
                        ],
                        'step two' => [
                            StepData::KEY_USE => 'step_import_name',
                            StepData::KEY_ELEMENTS => [
                                'heading' => 'malformed_page_element_reference',
                            ],
                        ],
                    ]
                ),
                'expectedException' => MalformedPageElementReferenceException::class,
                'expectedExceptionMessage' => 'Malformed page element reference "malformed_page_element_reference"',
                'expectedExceptionContext' =>  new ExceptionContext([
                    ExceptionContextInterface::KEY_TEST_NAME => 'test name',
                    ExceptionContextInterface::KEY_STEP_NAME => 'step two',
                ])
            ],
        ];
    }
}
