<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilContextAwareException\ExceptionContext\ExceptionContext;
use webignition\BasilContextAwareException\ExceptionContext\ExceptionContextInterface;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;
use webignition\BasilModelFactory\InvalidPageElementIdentifierException;

class InvalidPageElementIdentifierExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetIdentifier()
    {
        $identifier = (new DomIdentifier(
            new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
        ))->withAttributeName('attribute_name');

        $exception = new InvalidPageElementIdentifierException($identifier);

        $this->assertSame($identifier, $exception->getIdentifier());
    }

    public function testApplyExceptionContext()
    {
        $identifier = (new DomIdentifier(
            new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
        ))->withAttributeName('attribute_name');

        $exception = new InvalidPageElementIdentifierException($identifier);

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
