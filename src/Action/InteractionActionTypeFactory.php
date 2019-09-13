<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;
use webignition\BasilModelFactory\IdentifierTypes;

class InteractionActionTypeFactory implements ActionTypeFactoryInterface
{
    private $identifierFactory;

    public function __construct(IdentifierFactory $identifierFactory)
    {
        $this->identifierFactory = $identifierFactory;
    }

    public static function createFactory(): InteractionActionTypeFactory
    {
        return new InteractionActionTypeFactory(
            IdentifierFactory::createFactory()
        );
    }

    public function handles(string $type): bool
    {
        return in_array($type, [
            ActionTypes::CLICK,
            ActionTypes::SUBMIT,
            ActionTypes::WAIT_FOR,
        ]);
    }

    /**
     * @param string $actionString
     * @param string $type
     * @param string $arguments
     *
     * @return ActionInterface
     *
     * @throws InvalidIdentifierStringException
     * @throws InvalidActionTypeException
     */
    public function createForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        if (!$this->handles($type)) {
            throw new InvalidActionTypeException($type);
        }

        $identifier = $this->identifierFactory->create($arguments, [
            IdentifierTypes::ELEMENT_REFERENCE,
            IdentifierTypes::ELEMENT_SELECTOR,
            IdentifierTypes::PAGE_ELEMENT_REFERENCE,
        ]);

        if (null === $identifier) {
            throw new InvalidIdentifierStringException($arguments);
        }

        return new InteractionAction($actionString, $type, $identifier, $arguments);
    }
}
