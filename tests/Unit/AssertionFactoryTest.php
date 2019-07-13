<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\Value;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
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
        $simpleCssSelectorIdentifier = new Identifier(
            IdentifierTypes::CSS_SELECTOR,
            new Value(
                ValueTypes::STRING,
                '.selector'
            )
        );

        $simpleScalarValue = new Value(
            ValueTypes::STRING,
            'value'
        );

        return [
            'simple css selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is "value"',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'simple css selector with element reference, is, scalar value' => [
                'assertionString' => '"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '"{{ reference }} .selector" is "value"',
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '{{ reference }} .selector'
                        )
                    ),
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'simple css selector, is, data parameter value' => [
                'assertionString' => '".selector" is $data.name',
                'expectedAssertion' => new Assertion(
                    '".selector" is $data.name',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::DATA_PARAMETER,
                        '$data.name',
                        'data',
                        'name'
                    )
                ),
            ],
            'simple css selector, is, element parameter value' => [
                'actionString' => '".selector" is $elements.name',
                'expectedAssertion' => new Assertion(
                    '".selector" is $elements.name',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        'elements',
                        'name'
                    )
                ),
            ],
            'simple css selector, is, page object value' => [
                'actionString' => '".selector" is $page.url',
                'expectedAssertion' => new Assertion(
                    '".selector" is $page.url',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::PAGE_OBJECT_PROPERTY,
                        '$page.url',
                        'page',
                        'url'
                    )
                ),
            ],
            'simple css selector, is, browser object value' => [
                'actionString' => '".selector" is $browser.size',
                'expectedAssertion' => new Assertion(
                    '".selector" is $browser.size',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS,
                    new ObjectValue(
                        ValueTypes::BROWSER_OBJECT_PROPERTY,
                        '$browser.size',
                        'browser',
                        'size'
                    )
                ),
            ],
            'simple css selector, is, escaped quotes scalar value' => [
                'assertionString' => '".selector" is "\"value\""',
                'expectedAssertion' => new Assertion(
                    '".selector" is "\"value\""',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS,
                    new Value(
                        ValueTypes::STRING,
                        '"value"'
                    )
                ),
            ],
            'simple css selector, is, lacking value' => [
                'assertionString' => '".selector" is',
                'expectedAssertion' => new Assertion(
                    '".selector" is',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS
                ),
            ],
            'simple css selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not "value"',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS_NOT,
                    $simpleScalarValue
                ),
            ],
            'simple css selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::IS_NOT
                ),
            ],
            'simple css selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new Assertion(
                    '".selector" exists',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::EXISTS
                ),
            ],
            'simple css selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" exists "value"',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::EXISTS
                ),
            ],
            'simple css selector, exists, data parameter value is ignored' => [
                'assertionString' => '".selector" exists $data.name',
                'expectedAssertion' => new Assertion(
                    '".selector" exists $data.name',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::EXISTS
                ),
            ],
            'simple css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" includes "value"',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::INCLUDES,
                    $simpleScalarValue
                ),
            ],
            'simple css selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
                'expectedAssertion' => new Assertion(
                    '".selector" includes',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::INCLUDES
                ),
            ],
            'simple css selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes "value"',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::EXCLUDES,
                    $simpleScalarValue
                ),
            ],
            'simple css selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::EXCLUDES
                ),
            ],
            'simple css selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" matches "value"',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::MATCHES,
                    $simpleScalarValue
                ),
            ],
            'simple css selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
                'expectedAssertion' => new Assertion(
                    '".selector" matches',
                    $simpleCssSelectorIdentifier,
                    AssertionComparisons::MATCHES
                ),
            ],
            'comparison-including css selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '.selector is is-not exists not-exists includes excludes matches foo'
                        )
                    ),
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'simple xpath expression, is, scalar value' => [
                'assertionString' => '"//foo" is "value"',
                'expectedAssertion' => new Assertion(
                    '"//foo" is "value"',
                    new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//foo'
                        )
                    ),
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'comparison-including non-simple xpath expression, is, scalar value' => [
                'assertionString' =>
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                'expectedAssertion' => new Assertion(
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                    new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]'
                        )
                    ),
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'page model element reference, is, scalar value' => [
                'assertionString' => 'page_import_name.elements.element_name is "value"',
                'expectedAssertion' => new Assertion(
                    'page_import_name.elements.element_name is "value"',
                    new Identifier(
                        IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                        new Value(
                            ValueTypes::STRING,
                            'page_import_name.elements.element_name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name is "value"',
                'expectedAssertion' => new Assertion(
                    '$elements.name is "value"',
                    new Identifier(
                        IdentifierTypes::ELEMENT_PARAMETER,
                        new ObjectValue(
                            ValueTypes::ELEMENT_PARAMETER,
                            '$elements.name',
                            'elements',
                            'name'
                        )
                    ),
                    AssertionComparisons::IS,
                    $simpleScalarValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new Assertion(
                    '$page.url is "http://example.com/"',
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
                        'http://example.com/'
                    )
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new Assertion(
                    '$browser.size is 1024,768',
                    new Identifier(
                        IdentifierTypes::BROWSER_OBJECT_PARAMETER,
                        new ObjectValue(
                            ValueTypes::BROWSER_OBJECT_PROPERTY,
                            '$browser.size',
                            'browser',
                            'size'
                        )
                    ),
                    AssertionComparisons::IS,
                    new Value(
                        ValueTypes::STRING,
                        '1024,768'
                    )
                ),
            ],
        ];
    }

    public function testCreateFromEmptyAssertionString()
    {
        $assertionString = '';

        $assertion = $this->assertionFactory->createFromAssertionString($assertionString);

        $this->assertInstanceOf(AssertionInterface::class, $assertion);
        $this->assertSame($assertionString, $assertion->getAssertionString());
        $this->assertNull($assertion->getIdentifier());
        $this->assertSame('', $assertion->getComparison());
        $this->assertNull($assertion->getValue());
    }
}
