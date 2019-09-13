<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModelFactory\Action\WaitActionTypeFactory;

class WaitActionTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var WaitActionTypeFactory
     */
    private $actionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actionFactory = WaitActionTypeFactory::createFactory();
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
                'expectedHandles' => false,
            ],
            'submit' => [
                'type' => ActionTypes::SUBMIT,
                'expectedHandles' => false,
            ],
            'wait' => [
                'type' => ActionTypes::WAIT,
                'expectedHandles' => true,
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
}
