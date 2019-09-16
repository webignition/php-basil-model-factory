<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;

trait CssSelectorIdentifierDataProviderTrait
{
    public function cssSelectorIdentifierDataProvider(): array
    {
        $cssSelectorElementExpression = new ElementExpression('.listed-item', ElementExpressionType::CSS_SELECTOR);

        return [
            'css id selector' => [
                'identifierString' => '"#element-id"',
                'expectedIdentifier' => new DomIdentifier(
                    new ElementExpression('#element-id', ElementExpressionType::CSS_SELECTOR)
                ),
            ],
            'css class selector, position: null' => [
                'identifierString' => '".listed-item"',
                'expectedIdentifier' => new DomIdentifier($cssSelectorElementExpression),
            ],
            'css class selector; position: 1' => [
                'identifierString' => '".listed-item":1',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(1),
            ],
            'css class selector; position: 3' => [
                'identifierString' => '".listed-item":3',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(3),
            ],
            'css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(-1),
            ],
            'css class selector; position: -3' => [
                'identifierString' => '".listed-item":-3',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(-3),
            ],
            'css class selector; position: first' => [
                'identifierString' => '".listed-item":first',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(1),
            ],
            'css class selector; position: last' => [
                'identifierString' => '".listed-item":last',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(-1),
            ],
        ];
    }
}
