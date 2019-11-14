<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait VariableParameterIdentifierStringDataProviderTrait
{
    public function variableParameterIdentifierStringDataProvider(): array
    {
        return [
            'variable parameter: assertion: page parameter is value' => [
                'string' => '$page.title is "value"',
                'expectedIdentifierString' => '$page.title',
            ],
            'variable parameter: assertion: element parameter is value' => [
                'string' => '$elements.name is "value"',
                'expectedIdentifierString' => '$elements.name',
            ],
            'variable parameter: page parameter only' => [
                'string' => '$page.title',
                'expectedIdentifierString' => '$page.title',
            ],
            'variable parameter: page parameter is environment value' => [
                'string' => '$page.title is $env.KEY',
                'expectedIdentifierString' => '$page.title',
            ],
            'variable parameter: page parameter is environment value with default' => [
                'string' => '$page.title is $env.KEY|"default"',
                'expectedIdentifierString' => '$page.title',
            ],
            'variable parameter: assertion: environment parameter is value' => [
                'string' => '$env.KEY is "value"',
                'expectedIdentifierString' => '$env.KEY',
            ],
            'variable parameter: assertion: environment parameter with default without whitespace is value' => [
                'string' => '$env.KEY|"default" is "value"',
                'expectedIdentifierString' => '$env.KEY|"default"',
            ],
            'variable parameter: assertion: environment parameter with default with whitespace is value' => [
                'string' => '$env.KEY|"default value" is "value"',
                'expectedIdentifierString' => '$env.KEY|"default value"',
            ],
        ];
    }
}
