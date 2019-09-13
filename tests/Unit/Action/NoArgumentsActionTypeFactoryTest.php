<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModelFactory\Action\NoArgumentsActionTypeFactory;

class NoArgumentsActionTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NoArgumentsActionTypeFactory
     */
    private $actionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actionFactory = NoArgumentsActionTypeFactory::createFactory();
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
                'expectedHandles' => false,
            ],
            'wait-for' => [
                'type' => ActionTypes::WAIT_FOR,
                'expectedHandles' => false,
            ],
            'back' => [
                'type' => ActionTypes::BACK,
                'expectedHandles' => true,
            ],
            'forward' => [
                'type' => ActionTypes::FORWARD,
                'expectedHandles' => true,
            ],
            'reload' => [
                'type' => ActionTypes::RELOAD,
                'expectedHandles' => true,
            ],
        ];
    }
}
