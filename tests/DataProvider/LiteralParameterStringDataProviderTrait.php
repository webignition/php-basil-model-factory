<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait LiteralParameterStringDataProviderTrait
{
    public function literalParameterStringDataProvider(): array
    {
        return [
            'literal: assertion: page model reference is value' => [
                'string' => 'page.elements.name is "value"',
                'expectedIdentifierString' => 'page.elements.name',
            ],
            'literal: assertion: page model reference only' => [
                'string' => 'page.elements.name',
                'expectedIdentifierString' => 'page.elements.name',
            ],
        ];
    }
}
