<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\LiteralValue;

trait CssSelectorIdentifierDataProviderTrait
{
    public function cssSelectorIdentifierDataProvider(): array
    {
        return [
            'css id selector' => [
                'identifierString' => '"#element-id"',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('#element-id'),
                    1
                ),
            ],
            'css class selector, position: null' => [
                'identifierString' => '".listed-item"',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    1
                ),
            ],
            'css class selector; position: 1' => [
                'identifierString' => '".listed-item":1',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    1
                ),
            ],
            'css class selector; position: 3' => [
                'identifierString' => '".listed-item":3',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    3
                ),
            ],
            'css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    -1
                ),
            ],
            'css class selector; position: -3' => [
                'identifierString' => '".listed-item":-3',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    -3
                ),
            ],
            'css class selector; position: first' => [
                'identifierString' => '".listed-item":first',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    1
                ),
            ],
            'css class selector; position: last' => [
                'identifierString' => '".listed-item":last',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    -1
                ),
            ],
        ];
    }
}
