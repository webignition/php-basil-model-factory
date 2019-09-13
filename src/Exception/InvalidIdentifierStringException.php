<?php

namespace webignition\BasilModelFactory\Exception;

class InvalidIdentifierStringException extends \Exception
{
    private $identifierString;

    public function __construct(string $identifierString)
    {
        parent::__construct('Invalid identifier string "' . $identifierString . '"');

        $this->identifierString = $identifierString;
    }

    public function getIdentifierString(): string
    {
        return $this->identifierString;
    }
}
