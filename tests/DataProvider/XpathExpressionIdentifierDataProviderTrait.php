<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;

trait XpathExpressionIdentifierDataProviderTrait
{
    public function xpathExpressionIdentifierDataProvider(): array
    {
        return [
            'xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]"',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//*[@id="element-id"]', ElementExpressionType::XPATH_EXPRESSION)
                ),
            ],
            'xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]"',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION)
                ),
            ],
            'xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                    1
                ),
            ],
            'xpath attribute selector; position: 3' => [
                'identifierString' => '"//input[@type="submit"]":3',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                    3
                ),
            ],
            'xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                    -1
                ),
            ],
            'xpath attribute selector; position: -3' => [
                'identifierString' => '"//input[@type="submit"]":-3',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                    -3
                ),
            ],
            'xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                    1
                ),
            ],
            'xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last',
                'expectedIdentifier' => new ElementIdentifier(
                    new ElementExpression('//input[@type="submit"]', ElementExpressionType::XPATH_EXPRESSION),
                    -1
                ),
            ],
        ];
    }
}
