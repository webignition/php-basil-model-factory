<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\PageElementReference;

trait PageElementReferenceIdentifierDataProviderTrait
{
    public function pageElementReferenceIdentifierDataProvider(): array
    {
        return [
            'page model element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
                'expectedIdentifier' => ReferenceIdentifier::createPageElementReferenceIdentifier(
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
