<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\ActionInterface as ActionDataInterface;
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

    /**
     * @param ActionDataInterface $actionData
     *
     * @return ActionInterface
     *
     * @throws InvalidActionTypeException
     */
    public function create(ActionDataInterface $actionData): ActionInterface
    {
        $type = (string) $actionData->getType();
        if (!$actionData instanceof WaitActionData) {
            throw new InvalidActionTypeException($type);
        }

        $duration = $this->valueFactory->createFromValueString($actionData->getDuration());

        return new WaitAction($actionData->getSource(), $duration);
    }
}
