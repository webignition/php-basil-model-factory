<?php

namespace webignition\BasilModelFactory\Exception;

class InvalidActionTypeException extends \Exception
{
    private $type;

    public function __construct(string $type)
    {
        parent::__construct('Invalid action type "' . $type . '"');

        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
