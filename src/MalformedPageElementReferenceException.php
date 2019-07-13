<?php

namespace webignition\BasilModelFactory;

use webignition\BasilContextAwareException\ContextAwareExceptionInterface;
use webignition\BasilContextAwareException\ContextAwareExceptionTrait;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilModel\PageElementReference\PageElementReference;

class MalformedPageElementReferenceException extends \Exception implements ContextAwareExceptionInterface
{
    use ContextAwareExceptionTrait;

    private $pageElementReference;

    public function __construct(PageElementReference $pageElementReference)
    {
        parent::__construct('Malformed page element reference "' . (string) $pageElementReference . '"');

        $this->pageElementReference = $pageElementReference;
        $this->exceptionContext = new ExceptionContext();
    }

    public function getPageElementReference(): PageElementReference
    {
        return $this->pageElementReference;
    }
}
