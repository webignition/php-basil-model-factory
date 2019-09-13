<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModelFactory\Action\InputActionTypeFactory;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;

class InputActionTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InputActionTypeFactory
     */
    private $actionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actionFactory = InputActionTypeFactory::createFactory();
    }

    /**
     * @dataProvider handlesDataProvider
     */
    public function testHandles(string $type, bool $expectedHandles)
    {
        $this->assertSame($expectedHandles, $this->actionFactory->handles($type));
    }

    public function handlesDataProvider(): array
    {
        return [
            'click' => [
                'type' => ActionTypes::CLICK,
                'expectedHandles' => false,
            ],
            'set' => [
                'type' => ActionTypes::SET,
                'expectedHandles' => true,
            ],
            'submit' => [
                'type' => ActionTypes::SUBMIT,
                'expectedHandles' => false,
            ],
            'wait' => [
                'type' => ActionTypes::WAIT,
                'expectedHandles' => false,
            ],
            'wait-for' => [
                'type' => ActionTypes::WAIT_FOR,
                'expectedHandles' => false,
            ],
            'back' => [
                'type' => ActionTypes::BACK,
                'expectedHandles' => false,
            ],
            'forward' => [
                'type' => ActionTypes::FORWARD,
                'expectedHandles' => false,
            ],
            'reload' => [
                'type' => ActionTypes::RELOAD,
                'expectedHandles' => false,
            ],
        ];
    }

    public function testCreateForActionTypeThrowsException()
    {
        $this->expectExceptionObject(new InvalidActionTypeException('click'));

        $this->actionFactory->createForActionType(
            'click ".selector"',
            ActionTypes::CLICK,
            '".selector"'
        );
    }
}
