<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\DomIdentifierValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
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
        $elementLocator = '.selector';

        $cssIdentifier = new DomIdentifier($elementLocator);
        $literalValue = new LiteralValue('value');

        $cssDomIdentifierValue = new DomIdentifierValue($cssIdentifier);

        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    $literalValue
                ),
            ],
            'css element selector, is, data parameter value' => [
                'assertionString' => '".selector" is $data.name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $data.name',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    new ObjectValue(ObjectValueType::DATA_PARAMETER, '$data.name', 'name')
                ),
            ],
            'css element selector, is, element parameter' => [
                'actionString' => '".selector" is $elements.name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $elements.name',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ELEMENT,
                        '$elements.name',
                        'name'
                    )
                ),
            ],
            'css element selector, is, page property' => [
                'actionString' => '".selector" is $page.url',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $page.url',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url')
                ),
            ],
            'css element selector, is, browser property' => [
                'actionString' => '".selector" is $browser.size',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $browser.size',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    new ObjectValue(ObjectValueType::BROWSER_PROPERTY, '$browser.size', 'size')
                ),
            ],
            'css element selector, is, attribute parameter' => [
                'actionString' => '".selector" is $elements.element_name.attribute_name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $elements.element_name.attribute_name',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ATTRIBUTE,
                        '$elements.element_name.attribute_name',
                        'element_name.attribute_name'
                    )
                ),
            ],
            'css attribute selector, is, attribute value' => [
                'actionString' => '".selector".data-heading-title is $elements.element_name.attribute_name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector".data-heading-title is $elements.element_name.attribute_name',
                    new DomIdentifierValue(
                        (new DomIdentifier($elementLocator))->withAttributeName('data-heading-title')
                    ),
                    AssertionComparison::IS,
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ATTRIBUTE,
                        '$elements.element_name.attribute_name',
                        'element_name.attribute_name'
                    )
                ),
            ],
            'css element selector, is, escaped quotes scalar value' => [
                'assertionString' => '".selector" is "\"value\""',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "\"value\""',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    new LiteralValue('"value"')
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is-not "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS_NOT,
                    $literalValue
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists',
                    $cssDomIdentifierValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css element selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css element selector, exists, data parameter value is ignored' => [
                'assertionString' => '".selector" exists $data.name',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists $data.name',
                    $cssDomIdentifierValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" includes "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::INCLUDES,
                    $literalValue
                ),
            ],
            'css element selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" excludes "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::EXCLUDES,
                    $literalValue
                ),
            ],
            'css element selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" matches "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::MATCHES,
                    $literalValue
                ),
            ],
            'comparison-including css element selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                    DomIdentifierValue::create('.selector is is-not exists not-exists includes excludes matches foo'),
                    AssertionComparison::IS,
                    $literalValue
                ),
            ],
            'comparison-including non-simple xpath expression, is, scalar value' => [
                'assertionString' =>
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                    DomIdentifierValue::create(
                        '//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]'
                    ),
                    AssertionComparison::IS,
                    $literalValue
                ),
            ],
            'page element reference, is, scalar value' => [
                'assertionString' => 'page_import_name.elements.element_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    'page_import_name.elements.element_name is "value"',
                    new PageElementReference(
                        'page_import_name.elements.element_name',
                        'page_import_name',
                        'element_name'
                    ),
                    AssertionComparison::IS,
                    $literalValue
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$elements.name is "value"',
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ELEMENT,
                        '$elements.name',
                        'name'
                    ),
                    AssertionComparison::IS,
                    $literalValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is "http://example.com/"',
                    new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url'),
                    AssertionComparison::IS,
                    new LiteralValue('http://example.com/')
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new ComparisonAssertion(
                    '$browser.size is 1024,768',
                    new ObjectValue(ObjectValueType::BROWSER_PROPERTY, '$browser.size', 'size'),
                    AssertionComparison::IS,
                    new LiteralValue('1024,768')
                ),
            ],
            'page object parameter, is, environment value' => [
                'actionString' => '$page.url is $env.KEY',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is $env.KEY',
                    new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url'),
                    AssertionComparison::IS,
                    new ObjectValue(ObjectValueType::ENVIRONMENT_PARAMETER, '$env.KEY', 'KEY')
                ),
            ],
            'page object parameter, is, environment value with default' => [
                'actionString' => '$page.url is $env.KEY|"default"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is $env.KEY|"default"',
                    new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url'),
                    AssertionComparison::IS,
                    new ObjectValue(
                        ObjectValueType::ENVIRONMENT_PARAMETER,
                        '$env.KEY|"default"',
                        'KEY',
                        'default'
                    )
                ),
            ],
            'page object parameter, is, environment value with default with whitespace' => [
                'actionString' => '$page.url is $env.KEY|"default value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is $env.KEY|"default value"',
                    new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url'),
                    AssertionComparison::IS,
                    new ObjectValue(
                        ObjectValueType::ENVIRONMENT_PARAMETER,
                        '$env.KEY|"default value"',
                        'KEY',
                        'default value'
                    )
                ),
            ],
            'environment value, is, environment value' => [
                'actionString' => '$env.KEY1 is $env.KEY2',
                'expectedAssertion' => new ComparisonAssertion(
                    '$env.KEY1 is $env.KEY2',
                    new ObjectValue(ObjectValueType::ENVIRONMENT_PARAMETER, '$env.KEY1', 'KEY1'),
                    AssertionComparison::IS,
                    new ObjectValue(ObjectValueType::ENVIRONMENT_PARAMETER, '$env.KEY2', 'KEY2')
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
}
