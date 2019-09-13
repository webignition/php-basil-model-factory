<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\UnrecognisedAction;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;

class ActionFactory
{
    /**
     * @var ActionTypeFactoryInterface[]
     */
    private $actionTypeFactories = [];

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
     * @param string $actionString
     *
     * @return ActionInterface
     *
     * @throws InvalidActionTypeException
     * @throws InvalidIdentifierStringException
     */
    public function createFromActionString(string $actionString): ActionInterface
    {
        $actionString = trim($actionString);

        $type = $actionString;
        $arguments = '';

        if (mb_substr_count($actionString, ' ') > 0) {
            list($type, $arguments) = explode(' ', $actionString, 2);
        }

        if ($this->inputActionTypeFactory->handles($type)) {
            return $this->inputActionTypeFactory->createForActionType($actionString, $type, $arguments);
        }

        if ($this->interactionActionTypeFactory->handles($type)) {
            return $this->interactionActionTypeFactory->createForActionType($actionString, $type, $arguments);
        }

        if ($this->noArgumentsActionTypeFactory->handles($type)) {
            return $this->noArgumentsActionTypeFactory->createForActionType($actionString, $type, $arguments);
        }

        if ($this->waitActionTypeFactory->handles($type)) {
            return $this->waitActionTypeFactory->createForActionType($actionString, $type, $arguments);
        }

        $actionTypeFactory = $this->findActionTypeFactory($type);

        if ($actionTypeFactory instanceof ActionTypeFactoryInterface) {
            return $actionTypeFactory->createForActionType($actionString, $type, $arguments);
        }

        return new UnrecognisedAction($actionString, $type, $arguments);
    }

    private function findActionTypeFactory(string $type): ?ActionTypeFactoryInterface
    {
        foreach ($this->actionTypeFactories as $typeParser) {
            if ($typeParser->handles($type)) {
                return $typeParser;
            }
        }

        return null;
    }
}
