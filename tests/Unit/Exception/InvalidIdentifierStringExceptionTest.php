<?php

namespace webignition\BasilModelFactory\Tests\Unit\Exception;

use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;

class InvalidIdentifierStringExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetIdentifierString()
    {
        $identifierString = 'invalid';

        $exception = new InvalidIdentifierStringException($identifierString);

        $this->assertSame($identifierString, $exception->getIdentifierString());
    }
}
