<?php

namespace webignition\BasilModelFactory\Exception;

use webignition\BasilContextAwareException\ContextAwareExceptionInterface;
use webignition\BasilContextAwareException\ContextAwareExceptionTrait;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;

class InvalidIdentifierStringException extends \Exception implements ContextAwareExceptionInterface
{
    use ContextAwareExceptionTrait;

    private $identifierString;

    public function __construct(string $identifierString)
    {
        parent::__construct('Invalid identifier string "' . $identifierString . '"');

        $this->identifierString = $identifierString;
        $this->exceptionContext = new ExceptionContext();
    }

    public function getIdentifierString(): string
    {
        return $this->identifierString;
    }
}
