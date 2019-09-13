<?php

namespace webignition\BasilModelFactory\Exception;

use webignition\BasilContextAwareException\ContextAwareExceptionTrait;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;

class MissingValueException extends \Exception
{
    use ContextAwareExceptionTrait;

    public function __construct()
    {
        parent::__construct();

        $this->exceptionContext = new ExceptionContext();
    }
}
