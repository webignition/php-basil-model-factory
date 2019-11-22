<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;

class ActionFactory
{
    private $inputActionTypeFactory;
    private $interactionActionTypeFactory;
    private $noArgumentsActionTypeFactory;
    private $waitActionTypeFactory;

    public function __construct()
    {
        $this->inputActionTypeFactory = InputActionTypeFactory::createFactory();
        $this->interactionActionTypeFactory = InteractionActionTypeFactory::createFactory();
        $this->noArgumentsActionTypeFactory = NoArgumentsActionTypeFactory::createFactory();
        $this->waitActionTypeFactory = WaitActionTypeFactory::createFactory();
    }

    public static function createFactory(): ActionFactory
    {
        return new ActionFactory();
    }

    /**
     * @param ActionData $actionData
     *
     * @return ActionInterface
     *
     * @throws InvalidActionTypeException
     * @throws InvalidIdentifierStringException
     * @throws MissingValueException
     */
    public function createFromActionData(ActionData $actionData): ActionInterface
    {
        $type = (string) $actionData->getType();

        if ($this->inputActionTypeFactory->handles($type)) {
            return $this->inputActionTypeFactory->create($actionData);
        }

        if ($this->interactionActionTypeFactory->handles($type)) {
            return $this->interactionActionTypeFactory->create($actionData);
        }

        if ($this->noArgumentsActionTypeFactory->handles($type)) {
            return $this->noArgumentsActionTypeFactory->create($actionData);
        }

        if ($this->waitActionTypeFactory->handles($type)) {
            return $this->waitActionTypeFactory->create($actionData);
        }

        throw new InvalidActionTypeException($type);
    }
}
