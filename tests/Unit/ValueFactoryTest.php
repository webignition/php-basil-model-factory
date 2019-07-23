<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\ElementValueInterface;
use webignition\BasilModel\Value\EnvironmentValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectNames;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModel\Value\ValueTypes;
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
                'expectedValue' => new ObjectValue(
                    ValueTypes::DATA_PARAMETER,
                    '$data.data_name',
                    ObjectNames::DATA,
                    'data_name'
                ),
            ],
            'element parameter' => [
                'valueString' => '$elements.element_name',
                'expectedValue' => new ObjectValue(
                    ValueTypes::ELEMENT_PARAMETER,
                    '$elements.element_name',
                    ObjectNames::ELEMENT,
                    'element_name'
                ),
            ],
            'page property' => [
                'valueString' => '$page.url',
                'expectedValue' => new ObjectValue(
                    ValueTypes::PAGE_OBJECT_PROPERTY,
                    '$page.url',
                    ObjectNames::PAGE,
                    'url'
                ),
            ],
            'browser property' => [
                'valueString' => '$browser.size',
                'expectedValue' => new ObjectValue(
                    ValueTypes::BROWSER_OBJECT_PROPERTY,
                    '$browser.size',
                    ObjectNames::BROWSER,
                    'size'
                ),
            ],
            'page element reference' => [
                'valueString' => 'page_import_name.elements.element_name',
                'expectedValue' => new ObjectValue(
                    ValueTypes::PAGE_ELEMENT_REFERENCE,
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
                'expectedValue' => new EnvironmentValue(
                    '$env.KEY',
                    'KEY'
                ),
            ],
            'environment parameter, has default' => [
                'valueString' => '$env.KEY|"default_value"',
                'expectedValue' => new EnvironmentValue(
                    '$env.KEY|"default_value"',
                    'KEY',
                    'default_value'
                ),
            ],
            'environment parameter, empty default' => [
                'valueString' => '$env.KEY|""',
                'expectedValue' => new EnvironmentValue(
                    '$env.KEY|""',
                    'KEY',
                    ''
                ),
            ],
            'environment parameter, missing default' => [
                'valueString' => '$env.KEY|',
                'expectedValue' => new EnvironmentValue(
                    '$env.KEY|',
                    'KEY',
                    ''
                ),
            ],
            'environment parameter, has escaped-quote default' => [
                'valueString' => '$env.KEY|"\"default_value\""',
                'expectedValue' => new EnvironmentValue(
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

    public function testCreateFromIdentifier()
    {
        $identifier = new Identifier(
            IdentifierTypes::CSS_SELECTOR,
            '.selector'
        );

        $value = $this->valueFactory->createFromIdentifier($identifier);

        $this->assertInstanceOf(ElementValueInterface::class, $value);
        $this->assertSame($identifier, $value->getIdentifier());
    }
}
