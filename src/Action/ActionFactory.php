<?php

namespace webignition\BasilModelFactory\Action;

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
     * @param string $actionString
     *
     * @return ActionInterface
     *
     * @throws InvalidActionTypeException
     * @throws InvalidIdentifierStringException
     * @throws MissingValueException
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

        throw new InvalidActionTypeException($type);
    }
}
