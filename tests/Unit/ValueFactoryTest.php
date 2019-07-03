<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

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
    public function testCreateFromValueString(string $valueString, string $expectedType, string $expectedValue)
    {
        $value = $this->valueFactory->createFromValueString($valueString);

        $this->assertInstanceOf(ValueInterface::class, $value);
        $this->assertEquals($expectedType, $value->getType());
        $this->assertEquals($expectedValue, $value->getValue());
    }

    public function createFromValueStringDataProvider(): array
    {
        return [
            'empty' => [
                'valueString' => '',
                'expectedType' => ValueTypes::STRING,
                'expectedValue' => '',
            ],
            'quoted string' => [
                'valueString' => '"value"',
                'expectedType' => ValueTypes::STRING,
                'expectedValue' => 'value',
            ],
            'unquoted string' => [
                'valueString' => 'value',
                'expectedType' => ValueTypes::STRING,
                'expectedValue' => 'value',
            ],
            'quoted string wrapped with escaped quotes' => [
                'valueString' => '"\"value\""',
                'expectedType' => ValueTypes::STRING,
                'expectedValue' => '"value"',
            ],
            'quoted string containing escaped quotes' => [
                'valueString' => '"v\"alu\"e"',
                'expectedType' => ValueTypes::STRING,
                'expectedValue' => 'v"alu"e',
            ],
            'data parameter' => [
                'valueString' => '$data.name',
                'expectedType' => ValueTypes::DATA_PARAMETER,
                'expectedValue' => '$data.name',
            ],
            'element parameter' => [
                'valueString' => '$elements.name',
                'expectedType' => ValueTypes::ELEMENT_PARAMETER,
                'expectedValue' => '$elements.name',
            ],
        ];
    }
}
