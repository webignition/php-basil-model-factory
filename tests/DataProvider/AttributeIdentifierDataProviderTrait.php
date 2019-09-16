<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;

trait AttributeIdentifierDataProviderTrait
{
    public function attributeIdentifierDataProvider(): array
    {
        $cssSelectorElementExpression = new ElementExpression('.listed-item', ElementExpressionType::CSS_SELECTOR);
        $xpathAttributeSelectorElementExpression = new ElementExpression(
            '//input[@type="submit"]',
            ElementExpressionType::XPATH_EXPRESSION
        );

        return [
            'attribute identifier: css class selector, position: null' => [
                'identifierString' => '".listed-item".attribute_name',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: 1' => [
                'identifierString' => '".listed-item":1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: first' => [
                'identifierString' => '".listed-item":first.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: last' => [
                'identifierString' => '".listed-item":last.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($cssSelectorElementExpression))
                    ->withPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]".attribute_name',
                'expectedIdentifier' => (new DomIdentifier(
                    new ElementExpression('//*[@id="element-id"]', ElementExpressionType::XPATH_EXPRESSION)
                ))
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]".attribute_name',
                'expectedIdentifier' => (new DomIdentifier($xpathAttributeSelectorElementExpression))
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($xpathAttributeSelectorElementExpression))
                    ->withPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($xpathAttributeSelectorElementExpression))
                    ->withPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($xpathAttributeSelectorElementExpression))
                    ->withPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last.attribute_name',
                'expectedIdentifier' => (new DomIdentifier($xpathAttributeSelectorElementExpression))
                    ->withPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
        ];
    }
}
