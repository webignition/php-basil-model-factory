<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\ActionInterface as ActionDataInterface;
use webignition\BasilDataStructure\Action\InteractionAction as InteractionActionData;
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
     * @param ActionDataInterface $actionData
     *
     * @return ActionInterface
     *
     * @throws InvalidActionTypeException
     * @throws InvalidIdentifierStringException
     */
    public function create(ActionDataInterface $actionData): ActionInterface
    {
        $type = (string) $actionData->getType();
        if (!$actionData instanceof InteractionActionData) {
            throw new InvalidActionTypeException($type);
        }

        $identifierString = (string) $actionData->getIdentifier();

        $identifier = $this->identifierFactory->create($identifierString, [
            IdentifierTypes::ELEMENT_REFERENCE,
            IdentifierTypes::ELEMENT_SELECTOR,
            IdentifierTypes::PAGE_ELEMENT_REFERENCE,
        ]);

        if (null === $identifier) {
            throw new InvalidIdentifierStringException($identifierString);
        }

        return new InteractionAction(
            $actionData->getSource(),
            $type,
            $identifier,
            (string) $actionData->getArguments()
        );
    }
}
