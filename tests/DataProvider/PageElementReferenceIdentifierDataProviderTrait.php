<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\PageElementReference;

trait PageElementReferenceIdentifierDataProviderTrait
{
    public function pageElementReferenceIdentifierDataProvider(): array
    {
        return [
            'page model element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
                'expectedIdentifier' => new ReferenceIdentifier(
                    IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                    new PageElementReference(
                        'page_import_name.elements.element_name',
                        'page_import_name',
                        'element_name'
                    )
                ),
            ],
        ];
    }
}
