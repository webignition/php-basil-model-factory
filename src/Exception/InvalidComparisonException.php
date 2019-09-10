<?php

namespace webignition\BasilModelFactory\Exception;

class InvalidComparisonException extends \Exception
{
    private $assertionString;
    private $comparison;

    public function __construct(string $assertionString, string $comparison)
    {
        parent::__construct('Invalid comparison "' . $comparison . '"');

        $this->assertionString = $assertionString;
        $this->comparison = $comparison;
    }

    public function getAssertionString(): string
    {
        return $this->assertionString;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }
}
