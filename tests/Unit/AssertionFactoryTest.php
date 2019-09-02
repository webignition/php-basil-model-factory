<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Identifier\AttributeIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\AttributeValue;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectNames;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilModelFactory\AssertionFactory;

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
        $cssSelectorIdentifier = new ElementIdentifier(
            LiteralValue::createCssSelectorValue('.selector')
        );

        $cssSelectorIdentifierWithPosition1 = new ElementIdentifier(
            LiteralValue::createCssSelectorValue('.selector'),
            1
        );

        $literalValue = LiteralValue::createStringValue('value');

        $cssSelectorElementValue = new ElementValue($cssSelectorIdentifier);
        $cssSelectorWithPosition1ElementValue = new ElementValue($cssSelectorIdentifierWithPosition1);

        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css element selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1 is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":1 is "value"',
                    $cssSelectorWithPosition1ElementValue,
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css element selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2 is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":2 is "value"',
                    new ElementValue(new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.selector'),
                        2
                    )),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css element selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":first is "value"',
                    $cssSelectorWithPosition1ElementValue,
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css element selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":last is "value"',
                    new ElementValue(new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.selector'),
                        -1
                    )),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '".selector".attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector".attribute_name is "value"',
                    new AttributeValue(
                        new AttributeIdentifier(
                            new ElementIdentifier(LiteralValue::createCssSelectorValue('.selector')),
                            'attribute_name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css attribute selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1.attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":1.attribute_name is "value"',
                    new AttributeValue(
                        new AttributeIdentifier(
                            $cssSelectorIdentifierWithPosition1,
                            'attribute_name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css attribute selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2.attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":2.attribute_name is "value"',
                    new AttributeValue(
                        new AttributeIdentifier(
                            new ElementIdentifier(LiteralValue::createCssSelectorValue('.selector'), 2),
                            'attribute_name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css attribute selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first.attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":first.attribute_name is "value"',
                    new AttributeValue(
                        new AttributeIdentifier(
                            $cssSelectorIdentifierWithPosition1,
                            'attribute_name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css attribute selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last.attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector":last.attribute_name is "value"',
                    new AttributeValue(
                        new AttributeIdentifier(
                            new ElementIdentifier(LiteralValue::createCssSelectorValue('.selector'), -1),
                            'attribute_name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css element selector with element reference, is, scalar value' => [
                'assertionString' => '"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '"{{ reference }} .selector" is "value"',
                    new ElementValue(
                        new ElementIdentifier(
                            LiteralValue::createCssSelectorValue('{{ reference }} .selector')
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css element selector, is, data parameter value' => [
                'assertionString' => '".selector" is $data.name',
                'expectedAssertion' => new Assertion(
                    '".selector" is $data.name',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::DATA_PARAMETER,
                        '$data.name',
                        'data',
                        'name'
                    )
                ),
            ],
            'css element selector, is, element parameter value' => [
                'actionString' => '".selector" is $elements.name',
                'expectedAssertion' => new Assertion(
                    '".selector" is $elements.name',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        'elements',
                        'name'
                    )
                ),
            ],
            'css element selector, is, page object value' => [
                'actionString' => '".selector" is $page.url',
                'expectedAssertion' => new Assertion(
                    '".selector" is $page.url',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::PAGE_OBJECT_PROPERTY,
                        '$page.url',
                        'page',
                        'url'
                    )
                ),
            ],
            'css element selector, is, browser object value' => [
                'actionString' => '".selector" is $browser.size',
                'expectedAssertion' => new Assertion(
                    '".selector" is $browser.size',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::BROWSER_OBJECT_PROPERTY,
                        '$browser.size',
                        'browser',
                        'size'
                    )
                ),
            ],
            'css element selector, is, attribute parameter' => [
                'actionString' => '".selector" is $elements.element_name.attribute_name',
                'expectedAssertion' => new Assertion(
                    '".selector" is $elements.element_name.attribute_name',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::ATTRIBUTE_PARAMETER,
                        '$elements.element_name.attribute_name',
                        ObjectNames::ELEMENT,
                        'element_name.attribute_name'
                    )
                ),
            ],
            'css attribute selector selector, is, attribute value' => [
                'actionString' => '".selector".data-heading-title is $elements.element_name.attribute_name',
                'expectedAssertion' => new Assertion(
                    '".selector".data-heading-title is $elements.element_name.attribute_name',
                    new AttributeValue(
                        new AttributeIdentifier(
                            new ElementIdentifier(LiteralValue::createCssSelectorValue('.selector')),
                            'data-heading-title'
                        )
                    ),
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::ATTRIBUTE_PARAMETER,
                        '$elements.element_name.attribute_name',
                        ObjectNames::ELEMENT,
                        'element_name.attribute_name'
                    )
                ),
            ],
            'css element selector, is, escaped quotes scalar value' => [
                'assertionString' => '".selector" is "\"value\""',
                'expectedAssertion' => new Assertion(
                    '".selector" is "\"value\""',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    LiteralValue::createStringValue('"value"')
                ),
            ],
            'css element selector, is, lacking value' => [
                'assertionString' => '".selector" is',
                'expectedAssertion' => new Assertion(
                    '".selector" is',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS_NOT,
                    $literalValue
                ),
            ],
            'css element selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS_NOT
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new Assertion(
                    '".selector" exists',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXISTS
                ),
            ],
            'css element selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" exists "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXISTS
                ),
            ],
            'css element selector, exists, data parameter value is ignored' => [
                'assertionString' => '".selector" exists $data.name',
                'expectedAssertion' => new Assertion(
                    '".selector" exists $data.name',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXISTS
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" includes "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::INCLUDES,
                    $literalValue
                ),
            ],
            'css element selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
                'expectedAssertion' => new Assertion(
                    '".selector" includes',
                    $cssSelectorElementValue,
                    AssertionComparisons::INCLUDES
                ),
            ],
            'css element selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXCLUDES,
                    $literalValue
                ),
            ],
            'css element selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXCLUDES
                ),
            ],
            'css element selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" matches "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::MATCHES,
                    $literalValue
                ),
            ],
            'css element selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
                'expectedAssertion' => new Assertion(
                    '".selector" matches',
                    $cssSelectorElementValue,
                    AssertionComparisons::MATCHES
                ),
            ],
            'comparison-including css element selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                    new ElementValue(
                        new ElementIdentifier(
                            LiteralValue::createCssSelectorValue(
                                '.selector is is-not exists not-exists includes excludes matches foo'
                            )
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'simple xpath expression, is, scalar value' => [
                'assertionString' => '"//foo" is "value"',
                'expectedAssertion' => new Assertion(
                    '"//foo" is "value"',
                    new ElementValue(
                        new ElementIdentifier(
                            LiteralValue::createXpathExpressionValue('//foo')
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'comparison-including non-simple xpath expression, is, scalar value' => [
                'assertionString' =>
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                'expectedAssertion' => new Assertion(
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                    new ElementValue(
                        new ElementIdentifier(
                            LiteralValue::createXpathExpressionValue(
                                '//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]'
                            )
                        )
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'page element reference, is, scalar value' => [
                'assertionString' => 'page_import_name.elements.element_name is "value"',
                'expectedAssertion' => new Assertion(
                    'page_import_name.elements.element_name is "value"',
                    new ObjectValue(
                        ValueTypes::PAGE_ELEMENT_REFERENCE,
                        'page_import_name.elements.element_name',
                        'page_import_name',
                        'element_name'
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name is "value"',
                'expectedAssertion' => new Assertion(
                    '$elements.name is "value"',
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        ObjectNames::ELEMENT,
                        'name'
                    ),
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new Assertion(
                    '$page.url is "http://example.com/"',
                    new ObjectValue(
                        ValueTypes::PAGE_OBJECT_PROPERTY,
                        '$page.url',
                        ObjectNames::PAGE,
                        'url'
                    ),
                    AssertionComparisons::IS,
                    LiteralValue::createStringValue('http://example.com/')
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new Assertion(
                    '$browser.size is 1024,768',
                    new ObjectValue(
                        ValueTypes::BROWSER_OBJECT_PROPERTY,
                        '$browser.size',
                        ObjectNames::BROWSER,
                        'size'
                    ),
                    AssertionComparisons::IS,
                    LiteralValue::createStringValue('1024,768')
                ),
            ],
        ];
    }

    public function testCreateFromEmptyAssertionString()
    {
        $assertionString = '';

        $assertion = $this->assertionFactory->createFromAssertionString($assertionString);

        $this->assertInstanceOf(AssertionInterface::class, $assertion);
        $this->assertEquals('', $assertion->getAssertionString());
        $this->assertNull($assertion->getExaminedValue());
        $this->assertNull($assertion->getComparison());
        $this->assertNull($assertion->getExpectedValue());
    }
}
