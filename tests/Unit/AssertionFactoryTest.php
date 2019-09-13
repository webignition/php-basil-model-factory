<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\AssertableComparisonAssertion;
use webignition\BasilModel\Assertion\AssertableExaminationAssertion;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\Identifier\AttributeIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\Assertion\AssertableExaminedValue;
use webignition\BasilModel\Value\Assertion\AssertableExpectedValue;
use webignition\BasilModel\Value\Assertion\ExaminedValue;
use webignition\BasilModel\Value\Assertion\ExpectedValue;
use webignition\BasilModel\Value\AttributeReference;
use webignition\BasilModel\Value\AttributeValue;
use webignition\BasilModel\Value\BrowserProperty;
use webignition\BasilModel\Value\DataParameter;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;
use webignition\BasilModel\Value\ElementReference;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModel\Value\PageProperty;
use webignition\BasilModelFactory\AssertionFactory;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;

class AssertionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AssertionFactory
     */
    private $assertionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertionFactory = AssertionFactory::createFactory();
    }

    /**
     * @dataProvider createFromAssertionString
     */
    public function testCreateFromAssertionString(string $assertionString, AssertionInterface $expectedAssertion)
    {
        $assertion = $this->assertionFactory->createFromAssertionString($assertionString);

        $this->assertInstanceOf(AssertionInterface::class, $assertion);
        $this->assertEquals($expectedAssertion, $assertion);
    }

    public function createFromAssertionString(): array
    {
        $cssSelector = new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR);
        $cssSelectorWithElementReference = new ElementExpression(
            '{{ reference }} .selector',
            ElementExpressionType::CSS_SELECTOR
        );

        $cssIdentifier = new ElementIdentifier($cssSelector);
        $cssIdentifierWithPosition1 = new ElementIdentifier($cssSelector, 1);
        $cssIdentifierWithPosition2 = new ElementIdentifier($cssSelector, 2);
        $cssIdentifierWithPositionMinus1 = new ElementIdentifier($cssSelector, -1);
        $cssIdentifierWithElementReference = new ElementIdentifier($cssSelectorWithElementReference);

        $literalValue = new LiteralValue('value');

        $cssElementValue = new ElementValue($cssIdentifier);
        $cssElementValueWithPosition1 = new ElementValue($cssIdentifierWithPosition1);
        $cssElementValueWithPosition2 = new ElementValue($cssIdentifierWithPosition2);
        $cssElementValueWithPositionMinus1 = new ElementValue($cssIdentifierWithPositionMinus1);
        $cssElementValueWithElementReference = new ElementValue($cssIdentifierWithElementReference);

        $cssSelectorAttributeValue = new AttributeValue(
            new AttributeIdentifier($cssIdentifier, 'attribute_name')
        );

        $cssSelectorWithPosition1AttributeValue = new AttributeValue(
            new AttributeIdentifier($cssIdentifierWithPosition1, 'attribute_name')
        );

        $cssSelectorWithPosition2AttributeValue = new AttributeValue(
            new AttributeIdentifier($cssIdentifierWithPosition2, 'attribute_name')
        );

        $cssSelectorWithPositionMinus1AttributeValue = new AttributeValue(
            new AttributeIdentifier($cssIdentifierWithPositionMinus1, 'attribute_name')
        );

        $cssSelectorExaminedValue = new ExaminedValue($cssElementValue);
        $literalExpectedValue = new ExpectedValue($literalValue);

        $cssSelectorWithPosition1ExaminedValue = new ExaminedValue(
            $cssElementValueWithPosition1
        );

        $cssSelectorWithPosition2ExaminedValue = new ExaminedValue(
            $cssElementValueWithPosition2
        );

        $cssSelectorWithPositionMinus1ExaminedValue = new ExaminedValue(
            $cssElementValueWithPositionMinus1
        );

        $cssSelectorWithElementReferenceExaminedValue = new ExaminedValue(
            $cssElementValueWithElementReference
        );

        $attributeValueExaminedValue = new ExaminedValue($cssSelectorAttributeValue);
        $attributeValueWithPosition1ExaminedValue = new ExaminedValue($cssSelectorWithPosition1AttributeValue);
        $attributeValueWithPosition2ExaminedValue = new ExaminedValue($cssSelectorWithPosition2AttributeValue);
        $attributeValueWithPositionMinus1ExaminedValue = new ExaminedValue(
            $cssSelectorWithPositionMinus1AttributeValue
        );

        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "value"',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1 is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":1 is "value"',
                    $cssSelectorWithPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2 is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":2 is "value"',
                    $cssSelectorWithPosition2ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":first is "value"',
                    $cssSelectorWithPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":last is "value"',
                    $cssSelectorWithPositionMinus1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '".selector".attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector".attribute_name is "value"',
                    $attributeValueExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":1.attribute_name is "value"',
                    $attributeValueWithPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":2.attribute_name is "value"',
                    $attributeValueWithPosition2ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":first.attribute_name is "value"',
                    $attributeValueWithPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":last.attribute_name is "value"',
                    $attributeValueWithPositionMinus1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with element reference, is, scalar value' => [
                'assertionString' => '"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"{{ reference }} .selector" is "value"',
                    $cssSelectorWithElementReferenceExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector, is, data parameter value' => [
                'assertionString' => '".selector" is $data.name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $data.name',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new DataParameter('$data.name', 'name')
                    )
                ),
            ],
            'css element selector, is, element parameter value' => [
                'actionString' => '".selector" is $elements.name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $elements.name',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new ElementReference('$elements.name', 'name')
                    )
                ),
            ],
            'css element selector, is, page object value' => [
                'actionString' => '".selector" is $page.url',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $page.url',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new PageProperty('$page.url', 'url')
                    )
                ),
            ],
            'css element selector, is, browser object value' => [
                'actionString' => '".selector" is $browser.size',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $browser.size',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new BrowserProperty('$browser.size', 'size')
                    )
                ),
            ],
            'css element selector, is, attribute parameter' => [
                'actionString' => '".selector" is $elements.element_name.attribute_name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $elements.element_name.attribute_name',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new AttributeReference(
                            '$elements.element_name.attribute_name',
                            'element_name.attribute_name'
                        )
                    )
                ),
            ],
            'css attribute selector,  is, attribute value' => [
                'actionString' => '".selector".data-heading-title is $elements.element_name.attribute_name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector".data-heading-title is $elements.element_name.attribute_name',
                    new ExaminedValue(
                        new AttributeValue(
                            new AttributeIdentifier(
                                $cssIdentifier,
                                'data-heading-title'
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new AttributeReference(
                            '$elements.element_name.attribute_name',
                            'element_name.attribute_name'
                        )
                    )
                ),
            ],
            'css element selector, is, escaped quotes scalar value' => [
                'assertionString' => '".selector" is "\"value\""',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "\"value\""',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new LiteralValue('"value"')
                    )
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is-not "value"',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS_NOT,
                    $literalExpectedValue
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists',
                    $cssSelectorExaminedValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css element selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists "value"',
                    $cssSelectorExaminedValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css element selector, exists, data parameter value is ignored' => [
                'assertionString' => '".selector" exists $data.name',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists $data.name',
                    $cssSelectorExaminedValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" includes "value"',
                    $cssSelectorExaminedValue,
                    AssertionComparison::INCLUDES,
                    $literalExpectedValue
                ),
            ],
            'css element selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" excludes "value"',
                    $cssSelectorExaminedValue,
                    AssertionComparison::EXCLUDES,
                    $literalExpectedValue
                ),
            ],
            'css element selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" matches "value"',
                    $cssSelectorExaminedValue,
                    AssertionComparison::MATCHES,
                    $literalExpectedValue
                ),
            ],
            'comparison-including css element selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                    new ExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression(
                                    '.selector is is-not exists not-exists includes excludes matches foo',
                                    ElementExpressionType::CSS_SELECTOR
                                )
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'simple xpath expression, is, scalar value' => [
                'assertionString' => '"//foo" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"//foo" is "value"',
                    new ExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression('//foo', ElementExpressionType::XPATH_EXPRESSION)
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'comparison-including non-simple xpath expression, is, scalar value' => [
                'assertionString' =>
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                    new ExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression(
                                    '//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]',
                                    ElementExpressionType::XPATH_EXPRESSION
                                )
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'page element reference, is, scalar value' => [
                'assertionString' => 'page_import_name.elements.element_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    'page_import_name.elements.element_name is "value"',
                    new ExaminedValue(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$elements.name is "value"',
                    new ExaminedValue(new ElementReference('$elements.name', 'name')),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is "http://example.com/"',
                    new ExaminedValue(new PageProperty('$page.url', 'url')),
                    AssertionComparison::IS,
                    new ExpectedValue(new LiteralValue('http://example.com/'))
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new ComparisonAssertion(
                    '$browser.size is 1024,768',
                    new ExaminedValue(new BrowserProperty('$browser.size', 'size')),
                    AssertionComparison::IS,
                    new ExpectedValue(new LiteralValue('1024,768'))
                ),
            ],
        ];
    }

    public function testCreateFromEmptyAssertionString()
    {
        $this->expectException(EmptyAssertionStringException::class);

        $this->assertionFactory->createFromAssertionString('');
    }

    /**
     * @dataProvider createAssertableAssertionReturnsAssertionDataProvider
     */
    public function testCreateAssertableAssertionReturnsAssertion(AssertionInterface $assertion)
    {
        $this->assertSame(
            $assertion,
            $this->assertionFactory->createAssertableAssertion($assertion)
        );
    }

    public function createAssertableAssertionReturnsAssertionDataProvider(): array
    {
        return [
            'non-examination, non-comparison' => [
                'assertion' => \Mockery::mock(AssertionInterface::class),
            ],
        ];
    }

    /**
     * @dataProvider createAssertableAssertionDataProvider
     */
    public function testCreateAssertableAssertion(
        AssertionInterface $assertion,
        AssertionInterface $expectedAssertion
    ) {
        $assertableAssertion = $this->assertionFactory->createAssertableAssertion($assertion);

        $this->assertNotSame($assertion, $assertableAssertion);
        $this->assertEquals($expectedAssertion, $assertableAssertion);
    }

    public function createAssertableAssertionDataProvider(): array
    {
        return [
            'examination assertion' => [
                'assertion' => new ExaminationAssertion(
                    '".selector" exists',
                    new ExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                            )
                        )
                    ),
                    AssertionComparison::EXISTS
                ),
                'expectedAssertion' => new AssertableExaminationAssertion(
                    '".selector" exists',
                    new AssertableExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                            )
                        )
                    ),
                    AssertionComparison::EXISTS
                )
            ],
            'comparison assertion' => [
                'assertion' => new ComparisonAssertion(
                    '".selector" is "foo"',
                    new ExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                            )
                        )
                    ),
                    AssertionComparison::EXISTS,
                    new ExpectedValue(
                        new LiteralValue('foo')
                    )
                ),
                'expectedAssertion' => new AssertableComparisonAssertion(
                    '".selector" is "foo"',
                    new AssertableExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                            )
                        )
                    ),
                    AssertionComparison::EXISTS,
                    new AssertableExpectedValue(
                        new LiteralValue('foo')
                    )
                )
            ],
        ];
    }

    /**
     * @dataProvider createFromAssertionStringThrowsMissingValueExceptionDataProvider
     */
    public function testCreateFromAssertionStringThrowsMissingValueException(string $assertionString)
    {
        $this->expectException(MissingValueException::class);

        $this->assertionFactory->createFromAssertionString($assertionString);
    }

    public function createFromAssertionStringThrowsMissingValueExceptionDataProvider(): array
    {
        return [
            'css element selector, is, lacking value' => [
                'assertionString' => '".selector" is',
            ],
            'css element selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
            ],
            'css element selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
            ],
            'css element selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
            ],
            'css element selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
            ],
        ];
    }

    /**
     * @dataProvider createAssertableAssertionFromStringDataProvider
     */
    public function testCreateAssertableAssertionFromString(
        string $assertionString,
        AssertionInterface $expectedAssertion
    ) {
        $this->assertEquals(
            $expectedAssertion,
            $this->assertionFactory->createAssertableAssertionFromString($assertionString)
        );
    }

    public function createAssertableAssertionFromStringDataProvider(): array
    {
        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new AssertableComparisonAssertion(
                    '".selector" is "value"',
                    new AssertableExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    new AssertableExpectedValue(
                        new LiteralValue('value')
                    )
                ),
            ],
        ];
    }
}
