<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\AttributeIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;

trait AttributeIdentifierDataProviderTrait
{
    public function attributeIdentifierDataProvider(): array
    {
        $cssSelectorElementIdentifier = new ElementIdentifier(
            new ElementExpression('.listed-item', ElementExpressionType::CSS_SELECTOR)
        );

        $cssSelectorElementIdentifierWithPosition1 = new ElementIdentifier(
            new ElementExpression('.listed-item', ElementExpressionType::CSS_SELECTOR),
            1
        );

        return [
            'attribute identifier: css class selector, position: null' => [
                'identifierString' => '".listed-item".attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    $cssSelectorElementIdentifier,
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: 1' => [
                'identifierString' => '".listed-item":1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    $cssSelectorElementIdentifierWithPosition1,
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('.listed-item', ElementExpressionType::CSS_SELECTOR),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: first' => [
                'identifierString' => '".listed-item":first.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    $cssSelectorElementIdentifierWithPosition1,
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: last' => [
                'identifierString' => '".listed-item":last.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('.listed-item', ElementExpressionType::CSS_SELECTOR),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]".attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('//*[@id="element-id"]', ElementExpressionType::XPATH_EXPRESSION)
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]".attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION)
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
        ];
    }
}
