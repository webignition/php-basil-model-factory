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
        ];
    }
}
