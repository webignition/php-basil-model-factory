<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ExcludesAssertion;
use webignition\BasilModel\Assertion\ExistsAssertion;
use webignition\BasilModel\Assertion\IncludesAssertion;
use webignition\BasilModel\Assertion\IsAssertion;
use webignition\BasilModel\Assertion\IsNotAssertion;
use webignition\BasilModel\Assertion\MatchesAssertion;
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
use webignition\BasilModelFactory\Exception\InvalidComparisonException;

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
                'expectedAssertion' => new IsAssertion(
                    '".selector" is "value"',
                    $cssSelectorExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1 is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":1 is "value"',
                    $cssSelectorWithPosition1ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2 is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":2 is "value"',
                    $cssSelectorWithPosition2ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":first is "value"',
                    $cssSelectorWithPosition1ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":last is "value"',
                    $cssSelectorWithPositionMinus1ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '".selector".attribute_name is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector".attribute_name is "value"',
                    $attributeValueExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1.attribute_name is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":1.attribute_name is "value"',
                    $attributeValueWithPosition1ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2.attribute_name is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":2.attribute_name is "value"',
                    $attributeValueWithPosition2ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first.attribute_name is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":first.attribute_name is "value"',
                    $attributeValueWithPosition1ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last.attribute_name is "value"',
                'expectedAssertion' => new IsAssertion(
                    '".selector":last.attribute_name is "value"',
                    $attributeValueWithPositionMinus1ExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector with element reference, is, scalar value' => [
                'assertionString' => '"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new IsAssertion(
                    '"{{ reference }} .selector" is "value"',
                    $cssSelectorWithElementReferenceExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector, is, data parameter value' => [
                'assertionString' => '".selector" is $data.name',
                'expectedAssertion' => new IsAssertion(
                    '".selector" is $data.name',
                    $cssSelectorExaminedValue,
                    new AssertionExpectedValue(
                        new DataParameter('$data.name', 'name')
                    )
                ),
            ],
            'css element selector, is, element parameter value' => [
                'actionString' => '".selector" is $elements.name',
                'expectedAssertion' => new IsAssertion(
                    '".selector" is $elements.name',
                    $cssSelectorExaminedValue,
                    new AssertionExpectedValue(
                        new ElementReference('$elements.name', 'name')
                    )
                ),
            ],
            'css element selector, is, page object value' => [
                'actionString' => '".selector" is $page.url',
                'expectedAssertion' => new IsAssertion(
                    '".selector" is $page.url',
                    $cssSelectorExaminedValue,
                    new AssertionExpectedValue(
                        new PageProperty('$page.url', 'url')
                    )
                ),
            ],
            'css element selector, is, browser object value' => [
                'actionString' => '".selector" is $browser.size',
                'expectedAssertion' => new IsAssertion(
                    '".selector" is $browser.size',
                    $cssSelectorExaminedValue,
                    new AssertionExpectedValue(
                        new BrowserProperty('$browser.size', 'size')
                    )
                ),
            ],
            'css element selector, is, attribute parameter' => [
                'actionString' => '".selector" is $elements.element_name.attribute_name',
                'expectedAssertion' => new IsAssertion(
                    '".selector" is $elements.element_name.attribute_name',
                    $cssSelectorExaminedValue,
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
                'expectedAssertion' => new IsAssertion(
                    '".selector".data-heading-title is $elements.element_name.attribute_name',
                    new AssertionExaminedValue(
                        new AttributeValue(
                            new AttributeIdentifier(
                                $cssIdentifier,
                                'data-heading-title'
                            )
                        )
                    ),
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
                'expectedAssertion' => new IsAssertion(
                    '".selector" is "\"value\""',
                    $cssSelectorExaminedValue,
                    new AssertionExpectedValue(
                        new LiteralValue('"value"')
                    )
                ),
            ],
            'css element selector, is, lacking value' => [
                'assertionString' => '".selector" is',
                'expectedAssertion' => new IsAssertion(
                    '".selector" is',
                    $cssSelectorExaminedValue,
                    $emptyLiteralExpectedValue
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new IsNotAssertion(
                    '".selector" is-not "value"',
                    $cssSelectorExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
                'expectedAssertion' => new IsNotAssertion(
                    '".selector" is-not',
                    $cssSelectorExaminedValue,
                    $emptyLiteralExpectedValue
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new ExistsAssertion(
                    '".selector" exists',
                    $cssSelectorExaminedValue
                ),
            ],
            'css element selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new ExistsAssertion(
                    '".selector" exists "value"',
                    $cssSelectorExaminedValue
                ),
            ],
            'css element selector, exists, data parameter value is ignored' => [
                'assertionString' => '".selector" exists $data.name',
                'expectedAssertion' => new ExistsAssertion(
                    '".selector" exists $data.name',
                    $cssSelectorExaminedValue
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new IncludesAssertion(
                    '".selector" includes "value"',
                    $cssSelectorExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
                'expectedAssertion' => new IncludesAssertion(
                    '".selector" includes',
                    $cssSelectorExaminedValue,
                    $emptyLiteralExpectedValue
                ),
            ],
            'css element selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new ExcludesAssertion(
                    '".selector" excludes "value"',
                    $cssSelectorExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
                'expectedAssertion' => new ExcludesAssertion(
                    '".selector" excludes',
                    $cssSelectorExaminedValue,
                    $emptyLiteralExpectedValue
                ),
            ],
            'css element selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new MatchesAssertion(
                    '".selector" matches "value"',
                    $cssSelectorExaminedValue,
                    $literalExpectedValue
                ),
            ],
            'css element selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
                'expectedAssertion' => new MatchesAssertion(
                    '".selector" matches',
                    $cssSelectorExaminedValue,
                    $emptyLiteralExpectedValue
                ),
            ],
            'comparison-including css element selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new IsAssertion(
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
                    $literalExpectedValue
                ),
            ],
            'simple xpath expression, is, scalar value' => [
                'assertionString' => '"//foo" is "value"',
                'expectedAssertion' => new IsAssertion(
                    '"//foo" is "value"',
                    new AssertionExaminedValue(
                        new ElementValue(
                            new ElementIdentifier(
                                new XpathExpression('//foo')
                            )
                        )
                    ),
                    $literalExpectedValue
                ),
            ],
            'comparison-including non-simple xpath expression, is, scalar value' => [
                'assertionString' =>
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                'expectedAssertion' => new IsAssertion(
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
                    $literalExpectedValue
                ),
            ],
            'page element reference, is, scalar value' => [
                'assertionString' => 'page_import_name.elements.element_name is "value"',
                'expectedAssertion' => new IsAssertion(
                    'page_import_name.elements.element_name is "value"',
                    new AssertionExaminedValue(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    $literalExpectedValue
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name is "value"',
                'expectedAssertion' => new IsAssertion(
                    '$elements.name is "value"',
                    new AssertionExaminedValue(new ElementReference('$elements.name', 'name')),
                    $literalExpectedValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new IsAssertion(
                    '$page.url is "http://example.com/"',
                    new AssertionExaminedValue(new PageProperty('$page.url', 'url')),
                    new AssertionExpectedValue(new LiteralValue('http://example.com/'))
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new IsAssertion(
                    '$browser.size is 1024,768',
                    new AssertionExaminedValue(new BrowserProperty('$browser.size', 'size')),
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

    public function testCreateForUnknownComparison()
    {
        $this->expectException(InvalidComparisonException::class);

        $this->assertionFactory->createFromAssertionString('".selector" foo "value"');
    }
}
