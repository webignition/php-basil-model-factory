<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\Identifier\AttributeIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\AssertionExaminedValue;
use webignition\BasilModel\Value\AssertionExpectedValue;
use webignition\BasilModel\Value\AttributeReference;
use webignition\BasilModel\Value\AttributeValue;
use webignition\BasilModel\Value\BrowserProperty;
use webignition\BasilModel\Value\CssSelector;
use webignition\BasilModel\Value\DataParameter;
use webignition\BasilModel\Value\ElementReference;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModel\Value\PageProperty;
use webignition\BasilModel\Value\XpathExpression;
use webignition\BasilModelFactory\AssertionFactory;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;

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
        $cssSelector = new CssSelector('.selector');
        $cssSelectorWithElementReference = new CssSelector('{{ reference }} .selector');

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

        $cssSelectorExaminedValue = new AssertionExaminedValue($cssElementValue);
        $literalExpectedValue = new AssertionExpectedValue($literalValue);
        $emptyLiteralExpectedValue = new AssertionExpectedValue(
            new LiteralValue('')
        );

        $cssSelectorWithPosition1ExaminedValue = new AssertionExaminedValue(
            $cssElementValueWithPosition1
        );

        $cssSelectorWithPosition2ExaminedValue = new AssertionExaminedValue(
            $cssElementValueWithPosition2
        );

        $cssSelectorWithPositionMinus1ExaminedValue = new AssertionExaminedValue(
            $cssElementValueWithPositionMinus1
        );

        $cssSelectorWithElementReferenceExaminedValue = new AssertionExaminedValue(
            $cssElementValueWithElementReference
        );

        $attributeValueExaminedValue = new AssertionExaminedValue($cssSelectorAttributeValue);
        $attributeValueWithPosition1ExaminedValue = new AssertionExaminedValue($cssSelectorWithPosition1AttributeValue);
        $attributeValueWithPosition2ExaminedValue = new AssertionExaminedValue($cssSelectorWithPosition2AttributeValue);
        $attributeValueWithPositionMinus1ExaminedValue = new AssertionExaminedValue(
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
                    new AssertionExpectedValue(
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
                    new AssertionExpectedValue(
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
                    new AssertionExpectedValue(
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
                    new AssertionExpectedValue(
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
                    new AssertionExpectedValue(
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
                    new AssertionExaminedValue(
                        new AttributeValue(
                            new AttributeIdentifier(
                                $cssIdentifier,
                                'data-heading-title'
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    new AssertionExpectedValue(
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
                    new AssertionExpectedValue(
                        new LiteralValue('"value"')
                    )
                ),
            ],
            'css element selector, is, lacking value' => [
                'assertionString' => '".selector" is',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS,
                    $emptyLiteralExpectedValue
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
            'css element selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is-not',
                    $cssSelectorExaminedValue,
                    AssertionComparison::IS_NOT,
                    $emptyLiteralExpectedValue
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
            'css element selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" includes',
                    $cssSelectorExaminedValue,
                    AssertionComparison::INCLUDES,
                    $emptyLiteralExpectedValue
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
            'css element selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" excludes',
                    $cssSelectorExaminedValue,
                    AssertionComparison::EXCLUDES,
                    $emptyLiteralExpectedValue
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
            'css element selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" matches',
                    $cssSelectorExaminedValue,
                    AssertionComparison::MATCHES,
                    $emptyLiteralExpectedValue
                ),
            ],
            'comparison-including css element selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                    new AssertionExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new CssSelector(
                                    '.selector is is-not exists not-exists includes excludes matches foo'
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
                    new AssertionExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new XpathExpression('//foo')
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
                    new AssertionExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new XpathExpression(
                                    '//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]'
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
                    new AssertionExaminedValue(
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
                    new AssertionExaminedValue(new ElementReference('$elements.name', 'name')),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is "http://example.com/"',
                    new AssertionExaminedValue(new PageProperty('$page.url', 'url')),
                    AssertionComparison::IS,
                    new AssertionExpectedValue(new LiteralValue('http://example.com/'))
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new ComparisonAssertion(
                    '$browser.size is 1024,768',
                    new AssertionExaminedValue(new BrowserProperty('$browser.size', 'size')),
                    AssertionComparison::IS,
                    new AssertionExpectedValue(new LiteralValue('1024,768'))
                ),
            ],
        ];
    }

    public function testCreateFromEmptyAssertionString()
    {
        $this->expectException(EmptyAssertionStringException::class);

        $this->assertionFactory->createFromAssertionString('');
    }
}
