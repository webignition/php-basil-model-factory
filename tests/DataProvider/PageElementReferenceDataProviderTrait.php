<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait PageElementReferenceDataProviderTrait
{
    public function pageElementReferenceDataProvider(): array
    {
        return [
            'page model element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
            ],
        ];
    }
}
