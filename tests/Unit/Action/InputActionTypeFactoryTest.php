<?php

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilDataStructure\Action\InputAction as InputActionData;
use webignition\BasilDataStructure\Action\WaitAction as WaitActionData;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModelFactory\Action\InputActionTypeFactory;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;

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

    public function testCreateForWrongActionTypeThrowsException()
    {
        $this->expectExceptionObject(new InvalidActionTypeException('wait'));

        $this->actionFactory->create(new WaitActionData('wait 1', '1'));
    }

    /**
     * @dataProvider createForMissingIdentifierStringThrowsExceptionDataProvider
     */
    public function testCreateForMissingIdentifierThrowsException(InputActionData $actionData)
    {
        $this->expectExceptionObject(new InvalidIdentifierStringException($actionData->getIdentifier()));

        $this->actionFactory->create($actionData);
    }

    public function createForMissingIdentifierStringThrowsExceptionDataProvider(): array
    {
        return [
            'missing identifier string' => [
                'actionData' => new InputActionData(
                    'set to "value"',
                    'to "value"',
                    '',
                    '"value"'
                ),
            ],
            'empty identifier string' => [
                'actionData' => new InputActionData(
                    'set "" to "value"',
                    '"" to "value"',
                    '""',
                    '"value"'
                ),
            ],
        ];
    }
}
