<?php

namespace webignition\BasilModelFactory\Exception;

use webignition\BasilContextAwareException\ContextAwareExceptionInterface;
use webignition\BasilContextAwareException\ContextAwareExceptionTrait;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;

class InvalidActionTypeException extends \Exception implements ContextAwareExceptionInterface
{
    use ContextAwareExceptionTrait;

    private $type;

    public function __construct(string $type)
    {
        parent::__construct('Invalid action type "' . $type . '"');

        $this->type = $type;
        $this->exceptionContext = new ExceptionContext();
    }

    public function getType(): string
    {
        return $this->type;
    }
}
