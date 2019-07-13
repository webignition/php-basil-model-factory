<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;

class MalformedPageElementReferenceExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetPageElementReference()
    {
        $pageElementReference = new PageElementReference('');
        $exception = new MalformedPageElementReferenceException($pageElementReference);

        $this->assertSame($pageElementReference, $exception->getPageElementReference());
    }
}
