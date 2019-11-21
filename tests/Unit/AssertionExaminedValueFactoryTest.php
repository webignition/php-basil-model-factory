<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\DomIdentifierValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModelFactory\AssertionExaminedValueFactory;

class AssertionExaminedValueFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AssertionExaminedValueFactory
     */
    private $assertionExaminedValueFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertionExaminedValueFactory = AssertionExaminedValueFactory::createFactory();
    }

    /**
     * @dataProvider createFromAssertionString
     */
    public function testCreateFromAssertionString(string $identifierString, ValueInterface $expectedValue)
    {
        $value = $this->assertionExaminedValueFactory->create($identifierString);

        $this->assertInstanceOf(ValueInterface::class, $value);
        $this->assertEquals($expectedValue, $value);
    }

    public function createFromAssertionString(): array
    {
        $elementLocator = '.selector';
        $elementLocatorWithParentReference = '{{ reference }} .selector';

        $cssIdentifier = new DomIdentifier($elementLocator);
        $cssIdentifierWithPosition = new DomIdentifier($elementLocator, 1);
        $cssIdentifierWithElementReference = new DomIdentifier($elementLocatorWithParentReference);

        return [
            'css element identifier' => [
                'identifierString' => '".selector"',
                'expectedValue' => new DomIdentifierValue($cssIdentifier),
            ],
            'element identifier with position' => [
                'identifierString' => '".selector":1',
                'expectedValue' => new DomIdentifierValue($cssIdentifierWithPosition),
            ],
            'attribute identifier' => [
                'identifierString' => '".selector".attribute_name',
                'expectedValue' => new DomIdentifierValue(
                    $cssIdentifier->withAttributeName('attribute_name')
                ),
            ],
            'attribute identifier with position' => [
                'identifierString' => '".selector":1.attribute_name',
                'expectedValue' => new DomIdentifierValue(
                    $cssIdentifierWithPosition->withAttributeName('attribute_name')
                ),
            ],
            'element identifier with element reference' => [
                'identifierString' => '"{{ reference }} .selector"',
                'expectedValue' => new DomIdentifierValue($cssIdentifierWithElementReference),
            ],
            'xpath element identifier' => [
                'identifierString' => '"//foo"',
                'expectedValue' => DomIdentifierValue::create('//foo'),
            ],
            'page element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
                'expectedValue' => new PageElementReference(
                    'page_import_name.elements.element_name',
                    'page_import_name',
                    'element_name'
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name',
                'expectedValue' => new DomIdentifierReference(
                    DomIdentifierReferenceType::ELEMENT,
                    '$elements.name',
                    'name'
                ),
            ],
            'page parameter, is, scalar value' => [
                'actionString' => '$page.url',
                'expectedValue' => new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url'),
            ],
            'browser parameter, is, scalar value' => [
                'actionString' => '$browser.size',
                'expectedValue' => new ObjectValue(ObjectValueType::BROWSER_PROPERTY, '$browser.size', 'size'),
            ],
            'environment value' => [
                'actionString' => '$env.KEY1',
                'expectedValue' => new ObjectValue(ObjectValueType::ENVIRONMENT_PARAMETER, '$env.KEY1', 'KEY1'),
            ],
            'environment value with default' => [
                'actionString' => '$env.KEY1|"default1"',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY1|"default1"',
                    'KEY1',
                    'default1'
                ),
            ],
            'environment value with default with whitespace' => [
                'actionString' => '$env.KEY1|"default value"',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY1|"default value"',
                    'KEY1',
                    'default value'
                ),
            ],
        ];
    }
}
