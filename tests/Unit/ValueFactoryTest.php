<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\Value;
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

        $this->valueFactory = new ValueFactory();
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
            'quoted string' => [
                'valueString' => '"value"',
                'expectedValue' => new Value(
                    ValueTypes::STRING,
                    'value'
                ),
            ],
            'unquoted string' => [
                'valueString' => 'value',
                'expectedValue' => new Value(
                    ValueTypes::STRING,
                    'value'
                ),
            ],
            'quoted string wrapped with escaped quotes' => [
                'valueString' => '"\"value\""',
                'expectedValue' => new Value(
                    ValueTypes::STRING,
                    '"value"'
                ),
            ],
            'quoted string containing escaped quotes' => [
                'valueString' => '"v\"alu\"e"',
                'expectedValue' => new Value(
                    ValueTypes::STRING,
                    'v"alu"e'
                ),
            ],
            'data parameter' => [
                'valueString' => '$data.data_name',
                'expectedValue' => new ObjectValue(
                    ValueTypes::DATA_PARAMETER,
                    '$data.data_name',
                    'data',
                    'data_name'
                ),
            ],
            'element parameter' => [
                'valueString' => '$elements.element_name',
                'expectedValue' => new ObjectValue(
                    ValueTypes::ELEMENT_PARAMETER,
                    '$elements.element_name',
                    'elements',
                    'element_name'
                ),
            ],
            'page property' => [
                'valueString' => '$page.url',
                'expectedValue' => new ObjectValue(
                    ValueTypes::PAGE_OBJECT_PROPERTY,
                    '$page.url',
                    'page',
                    'url'
                ),
            ],
            'browser property' => [
                'valueString' => '$browser.size',
                'expectedValue' => new ObjectValue(
                    ValueTypes::BROWSER_OBJECT_PROPERTY,
                    '$browser.size',
                    'browser',
                    'size'
                ),
            ],
        ];
    }
}
