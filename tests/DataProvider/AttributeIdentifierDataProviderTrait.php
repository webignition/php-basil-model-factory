<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\DomIdentifier;

trait AttributeIdentifierDataProviderTrait
{
    public function attributeIdentifierDataProvider(): array
    {
        return [
            'attribute identifier: css class selector, position: null' => [
                'identifierString' => '".listed-item".attribute_name',
                'expectedIdentifier' => (new DomIdentifier('.listed-item'))
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: 1' => [
                'identifierString' => '".listed-item":1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('.listed-item'))
                    ->withOrdinalPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('.listed-item'))
                    ->withOrdinalPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: first' => [
                'identifierString' => '".listed-item":first.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('.listed-item'))
                    ->withOrdinalPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: css class selector; position: last' => [
                'identifierString' => '".listed-item":last.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('.listed-item'))
                    ->withOrdinalPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]".attribute_name',
                'expectedIdentifier' => (new DomIdentifier('//*[@id="element-id"]'))
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]".attribute_name',
                'expectedIdentifier' => (new DomIdentifier('//input[@type="submit"]'))
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('//input[@type="submit"]'))
                    ->withOrdinalPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('//input[@type="submit"]'))
                    ->withOrdinalPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('//input[@type="submit"]'))
                    ->withOrdinalPosition(1)
                    ->withAttributeName('attribute_name'),
            ],
            'attribute identifier: xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last.attribute_name',
                'expectedIdentifier' => (new DomIdentifier('//input[@type="submit"]'))
                    ->withOrdinalPosition(-1)
                    ->withAttributeName('attribute_name'),
            ],
        ];
    }
}
