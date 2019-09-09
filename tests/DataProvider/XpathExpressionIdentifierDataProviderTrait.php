<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Value\XpathExpression;

trait XpathExpressionIdentifierDataProviderTrait
{
    public function xpathExpressionIdentifierDataProvider(): array
    {
        return [
            'xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]"',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//*[@id="element-id"]')
                ),
            ],
            'xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]"',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]')
                ),
            ],
            'xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]'),
                    1
                ),
            ],
            'xpath attribute selector; position: 3' => [
                'identifierString' => '"//input[@type="submit"]":3',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]'),
                    3
                ),
            ],
            'xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]'),
                    -1
                ),
            ],
            'xpath attribute selector; position: -3' => [
                'identifierString' => '"//input[@type="submit"]":-3',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]'),
                    -3
                ),
            ],
            'xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]'),
                    1
                ),
            ],
            'xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last',
                'expectedIdentifier' => new ElementIdentifier(
                    new XpathExpression('//input[@type="submit"]'),
                    -1
                ),
            ],
        ];
    }
}
