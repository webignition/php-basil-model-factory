<?php

namespace webignition\BasilModelFactory;

use webignition\BasilContextAwareException\ContextAwareExceptionInterface;
use webignition\BasilContextAwareException\ContextAwareExceptionTrait;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilModel\Identifier\IdentifierInterface;

class InvalidPageElementIdentifierException extends \Exception implements ContextAwareExceptionInterface
{
    use ContextAwareExceptionTrait;

    private $identifier;

    public function __construct(IdentifierInterface $identifier)
    {
        parent::__construct('Invalid page element identifier "' . (string) $identifier . '"');

        $this->identifier = $identifier;
        $this->exceptionContext = new ExceptionContext();
    }

    public function getIdentifier(): IdentifierInterface
    {
        return $this->identifier;
    }
}
