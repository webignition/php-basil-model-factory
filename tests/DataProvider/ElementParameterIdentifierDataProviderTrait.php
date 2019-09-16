<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;

trait ElementParameterIdentifierDataProviderTrait
{
    public function elementParameterIdentifierDataProvider(): array
    {
        return [
            'element parameter' => [
                'identifierString' => '$elements.name',
                'expectedIdentifier' => ReferenceIdentifier::createElementReferenceIdentifier(
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ELEMENT,
                        '$elements.name',
                        'name'
                    )
                ),
            ],
        ];
    }
}
