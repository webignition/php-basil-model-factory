<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModelFactory\Action\InteractionActionTypeFactory;

class InteractionActionTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InteractionActionTypeFactory
     */
    private $actionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actionFactory = InteractionActionTypeFactory::createFactory();
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
                'expectedHandles' => true,
            ],
            'set' => [
                'type' => ActionTypes::SET,
                'expectedHandles' => false,
            ],
            'submit' => [
                'type' => ActionTypes::SUBMIT,
                'expectedHandles' => true,
            ],
            'wait' => [
                'type' => ActionTypes::WAIT,
                'expectedHandles' => false,
            ],
            'wait-for' => [
                'type' => ActionTypes::WAIT_FOR,
                'expectedHandles' => true,
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
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid action type');

        $this->actionFactory->createForActionType(
            'set ".selector" to "value"',
            ActionTypes::SET,
            '".selector" to "value"'
        );
    }
}
