<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilDataStructure\Action\WaitAction as WaitActionData;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\WaitAction;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\ValueFactory;

class WaitActionTypeFactory implements ActionTypeFactoryInterface
{
    private $valueFactory;

    public function __construct(ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory(): WaitActionTypeFactory
    {
        return new WaitActionTypeFactory(
            ValueFactory::createFactory()
        );
    }

    public function handles(string $type): bool
    {
        return ActionTypes::WAIT === $type;
    }

    public function createForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        $duration = $this->valueFactory->createFromValueString($arguments);

        return new WaitAction($actionString, $duration);
    }

    /**
     * @param ActionData $actionData
     *
     * @return ActionInterface
     *
     * @throws InvalidActionTypeException
     */
    public function create(ActionData $actionData): ActionInterface
    {
        $type = $actionData->getType();
        if (!$actionData instanceof WaitActionData) {
            throw new InvalidActionTypeException($type);
        }

        $duration = $this->valueFactory->createFromValueString($actionData->getDuration());

        return new WaitAction($actionData->getSource(), $duration);
    }
}
