<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModelFactory\ValueFactory;

class ValueFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValueFactory
     */
    private $valueFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->valueFactory = ValueFactory::createFactory();
    }

    /**
     * @dataProvider createFromValueStringDataProvider
     */
    public function testCreateFromValueString(string $valueString, ValueInterface $expectedValue)
    {
        $value = $this->valueFactory->createFromValueString($valueString);

        $this->assertInstanceOf(ValueInterface::class, $value);
        $this->assertInstanceOf(get_class($expectedValue), $value);
        $this->assertEquals($expectedValue, $value);
    }

    public function createFromValueStringDataProvider(): array
    {
        return [
            'empty' => [
                'valueString' => '',
                'expectedValue' => new LiteralValue(''),
            ],
            'quoted string' => [
                'valueString' => '"value"',
                'expectedValue' => new LiteralValue('value'),
            ],
            'unquoted string' => [
                'valueString' => 'value',
                'expectedValue' => new LiteralValue('value'),
            ],
            'quoted string wrapped with escaped quotes' => [
                'valueString' => '"\"value\""',
                'expectedValue' => new LiteralValue('"value"'),
            ],
            'quoted string containing escaped quotes' => [
                'valueString' => '"v\"alu\"e"',
                'expectedValue' => new LiteralValue('v"alu"e'),
            ],
            'data parameter' => [
                'valueString' => '$data.data_name',
                'expectedValue' => new ObjectValue(ObjectValueType::DATA_PARAMETER, '$data.data_name', 'data_name'),
            ],
            'element parameter' => [
                'valueString' => '$elements.element_name',
                'expectedValue' => new DomIdentifierReference(
                    DomIdentifierReferenceType::ELEMENT,
                    '$elements.element_name',
                    'element_name'
                ),
            ],
            'attribute parameter' => [
                'valueString' => '$elements.element_name.attribute_name',
                'expectedValue' => new DomIdentifierReference(
                    DomIdentifierReferenceType::ATTRIBUTE,
                    '$elements.element_name.attribute_name',
                    'element_name.attribute_name'
                ),
            ],
            'page property' => [
                'valueString' => '$page.url',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::PAGE_PROPERTY,
                    '$page.url',
                    'url'
                ),
            ],
            'browser property' => [
                'valueString' => '$browser.size',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::BROWSER_PROPERTY,
                    '$browser.size',
                    'size'
                ),
            ],
            'page element reference' => [
                'valueString' => 'page_import_name.elements.element_name',
                'expectedValue' => new PageElementReference(
                    'page_import_name.elements.element_name',
                    'page_import_name',
                    'element_name'
                ),
            ],
            'page element reference string' => [
                'valueString' => '"page_import_name.elements.element_name"',
                'expectedValue' => new LiteralValue('page_import_name.elements.element_name'),
            ],
            'page element reference string with escaped quotes' => [
                'valueString' => '"\"page_import_name.elements.element_name\""',
                'expectedValue' => new LiteralValue('"page_import_name.elements.element_name"'),
            ],
            'environment parameter, no default' => [
                'valueString' => '$env.KEY',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY',
                    'KEY'
                ),
            ],
            'environment parameter, has default' => [
                'valueString' => '$env.KEY|"default_value"',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY|"default_value"',
                    'KEY',
                    'default_value'
                ),
            ],
            'environment parameter, has default containing whitespace' => [
                'valueString' => '$env.KEY|"default value"',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY|"default value"',
                    'KEY',
                    'default value'
                ),
            ],
            'environment parameter, empty default' => [
                'valueString' => '$env.KEY|""',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY|""',
                    'KEY',
                    ''
                ),
            ],
            'environment parameter, missing default' => [
                'valueString' => '$env.KEY|',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY|',
                    'KEY',
                    ''
                ),
            ],
            'environment parameter, has escaped-quote default' => [
                'valueString' => '$env.KEY|"\"default_value\""',
                'expectedValue' => new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.KEY|"\"default_value\""',
                    'KEY',
                    '"default_value"'
                ),
            ],
            'malformed page element reference' => [
                'valueString' => 'page_import_name.foo.element_name',
                'expectedValue' => new LiteralValue('page_import_name.foo.element_name'),
            ],
        ];
    }
}
