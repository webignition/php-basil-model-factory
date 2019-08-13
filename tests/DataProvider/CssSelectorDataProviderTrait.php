<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait CssSelectorDataProviderTrait
{
    public function cssSelectorDataProvider(): array
    {
        return [
            'css id selector' => [
                'identifierString' => '"#element-id"',
            ],
            'css class selector, position: null' => [
                'identifierString' => '".listed-item"',
            ],
            'css class selector; position: 1' => [
                'identifierString' => '".listed-item":1',
            ],
            'css class selector; position: 3' => [
                'identifierString' => '".listed-item":3',
            ],
            'css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1',
            ],
            'css class selector; position: -3' => [
                'identifierString' => '".listed-item":-3',
            ],
            'css class selector; position: first' => [
                'identifierString' => '".listed-item":first',
            ],
            'css class selector; position: last' => [
                'identifierString' => '".listed-item":last',
            ],
        ];
    }
}
