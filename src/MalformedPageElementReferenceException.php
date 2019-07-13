<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\PageElementReference\PageElementReference;

class MalformedPageElementReferenceException extends \Exception
{
    private $pageElementReference;

    public function __construct(PageElementReference $pageElementReference)
    {
        parent::__construct('Malformed page element reference "' . (string) $pageElementReference . '"');

        $this->pageElementReference = $pageElementReference;
    }

    public function getPageElementReference(): PageElementReference
    {
        return $this->pageElementReference;
    }
}
