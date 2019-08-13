<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueTypes;

trait ElementParameterIdentifierDataProviderTrait
{
    public function elementParameterIdentifierDataProvider(): array
    {
        return [
            'element parameter' => [
                'identifierString' => '$elements.name',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::ELEMENT_PARAMETER,
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        'elements',
                        'name'
                    )
                ),
            ],
        ];
    }
}
