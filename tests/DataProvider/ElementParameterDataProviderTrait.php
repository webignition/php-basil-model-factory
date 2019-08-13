<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait ElementParameterDataProviderTrait
{
    public function elementParameterDataProvider(): array
    {
        return [
            'element parameter' => [
                'identifierString' => '$elements.name',
            ],
        ];
    }
}
