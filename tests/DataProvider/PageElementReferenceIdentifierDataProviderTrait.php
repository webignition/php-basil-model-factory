<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueTypes;

trait PageElementReferenceIdentifierDataProviderTrait
{
    public function pageElementReferenceIdentifierDataProvider(): array
    {
        return [
            'page model element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                    new ObjectValue(
                        ValueTypes::PAGE_ELEMENT_REFERENCE,
                        'page_import_name.elements.element_name',
                        'page_import_name',
                        'element_name'
                    )
                ),
            ],
        ];
    }
}
