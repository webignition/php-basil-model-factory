<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Identifier\ElementIdentifier;
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

        $literalValue = LiteralValue::createStringValue('value');

        $cssSelectorElementValue = new ElementValue($cssSelectorIdentifier);

        return [
            'css selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    $literalValue
                ),
            ],
            'css selector with element reference, is, scalar value' => [
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
            'css selector, is, data parameter value' => [
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
            'css selector, is, element parameter value' => [
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
            'css selector, is, page object value' => [
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
            'css selector, is, browser object value' => [
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
            'css selector, is, escaped quotes scalar value' => [
                'assertionString' => '".selector" is "\"value\""',
                'expectedAssertion' => new Assertion(
                    '".selector" is "\"value\""',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS,
                    LiteralValue::createStringValue('"value"')
                ),
            ],
            'css selector, is, lacking value' => [
                'assertionString' => '".selector" is',
                'expectedAssertion' => new Assertion(
                    '".selector" is',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS
                ),
            ],
            'css selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS_NOT,
                    $literalValue
                ),
            ],
            'css selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not',
                    $cssSelectorElementValue,
                    AssertionComparisons::IS_NOT
                ),
            ],
            'css selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new Assertion(
                    '".selector" exists',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXISTS
                ),
            ],
            'css selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" exists "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXISTS
                ),
            ],
            'css selector, exists, data parameter value is ignored' => [
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
            'css selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
                'expectedAssertion' => new Assertion(
                    '".selector" includes',
                    $cssSelectorElementValue,
                    AssertionComparisons::INCLUDES
                ),
            ],
            'css selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXCLUDES,
                    $literalValue
                ),
            ],
            'css selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes',
                    $cssSelectorElementValue,
                    AssertionComparisons::EXCLUDES
                ),
            ],
            'css selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" matches "value"',
                    $cssSelectorElementValue,
                    AssertionComparisons::MATCHES,
                    $literalValue
                ),
            ],
            'css selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
                'expectedAssertion' => new Assertion(
                    '".selector" matches',
                    $cssSelectorElementValue,
                    AssertionComparisons::MATCHES
                ),
            ],
            'comparison-including css selector, is, scalar value' => [
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
