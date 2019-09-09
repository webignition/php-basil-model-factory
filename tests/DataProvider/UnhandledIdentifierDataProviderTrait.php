<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait UnhandledIdentifierDataProviderTrait
{
    public function unhandledIdentifierDataProvider(): array
    {
        return [
            'empty' => [
                'identifierString' => '',
            ],
        ];
    }
}
