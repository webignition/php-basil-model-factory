<?php

namespace webignition\BasilModelFactory\Tests\Unit\Exception;

use webignition\BasilModelFactory\Exception\InvalidActionTypeException;

class InvalidActionTypeExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetType()
    {
        $type = 'invalid';

        $exception = new InvalidActionTypeException($type);

        $this->assertSame($type, $exception->getType());
    }
}
