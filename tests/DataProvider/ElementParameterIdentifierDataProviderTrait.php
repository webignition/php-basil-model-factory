<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\ElementReference;

trait ElementParameterIdentifierDataProviderTrait
{
    public function elementParameterIdentifierDataProvider(): array
    {
        return [
            'element parameter' => [
                'identifierString' => '$elements.name',
                'expectedIdentifier' => new ReferenceIdentifier(
                    IdentifierTypes::ELEMENT_PARAMETER,
                    new ElementReference(
                        '$elements.name',
                        'name'
                    )
                ),
            ],
        ];
    }
}
