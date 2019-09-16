<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;

trait DomReferenceIdentifierDataProviderTrait
{
    public function domReferenceIdentifierDataProvider(): array
    {
        return [
            'element reference' => [
                'identifierString' => '$elements.element_name',
                'expectedIdentifier' => ReferenceIdentifier::createElementReferenceIdentifier(
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ELEMENT,
                        '$elements.element_name',
                        'element_name'
                    )
                ),
            ],
            'attribute reference' => [
                'identifierString' => '$elements.element_name.attribute_name',
                'expectedIdentifier' => ReferenceIdentifier::createElementReferenceIdentifier(
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ATTRIBUTE,
                        '$elements.element_name.attribute_name',
                        'element_name.attribute_name'
                    )
                ),
            ],
        ];
    }
}
