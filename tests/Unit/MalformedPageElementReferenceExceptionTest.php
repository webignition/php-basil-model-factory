<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
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

    public function testApplyExceptionContext()
    {
        $pageElementReference = new PageElementReference('');
        $exception = new MalformedPageElementReferenceException($pageElementReference);

        $exceptionContext = $exception->getExceptionContext();

        $this->assertEquals(new ExceptionContext([]), $exceptionContext);

        $testName = 'test name';
        $stepName = 'step name';
        $content = 'content';

        $exceptionContextValues = [
            ExceptionContextInterface::KEY_TEST_NAME => $testName,
            ExceptionContextInterface::KEY_STEP_NAME => $stepName,
            ExceptionContextInterface::KEY_CONTENT => $content,
        ];

        $exception->applyExceptionContext($exceptionContextValues);

        $this->assertEquals(new ExceptionContext($exceptionContextValues), $exceptionContext);
    }
}
