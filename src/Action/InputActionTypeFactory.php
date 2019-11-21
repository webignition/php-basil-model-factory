<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilDataStructure\Action\InputAction as InputActionData;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;
use webignition\BasilModelFactory\IdentifierTypes;
use webignition\BasilModelFactory\ValueFactory;

class InputActionTypeFactory implements ActionTypeFactoryInterface
{
    private $identifierFactory;
    private $valueFactory;

    public function __construct(IdentifierFactory $identifierFactory, ValueFactory $valueFactory)
    {
        $this->identifierFactory = $identifierFactory;
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory(): InputActionTypeFactory
    {
        return new InputActionTypeFactory(
            IdentifierFactory::createFactory(),
            ValueFactory::createFactory()
        );
    }

    public function handles(string $type): bool
    {
        return ActionTypes::SET === $type;
    }

    /**
     * @param ActionData $actionData
     *
     * @return ActionInterface
     *
     * @throws InvalidIdentifierStringException
     * @throws InvalidActionTypeException
     */
    public function create(ActionData $actionData): ActionInterface
    {
        $type = $actionData->getType();
        if (!$actionData instanceof InputActionData) {
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

        $value = $this->valueFactory->createFromValueString($actionData->getValue());

        return new InputAction($actionData->getSource(), $identifier, $value, (string) $actionData->getArguments());
    }
}
