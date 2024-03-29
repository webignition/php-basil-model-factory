<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\ActionInterface as ActionDataInterface;
use webignition\BasilDataStructure\Action\InputAction as InputActionData;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;
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
     * @param ActionDataInterface $actionData
     *
     * @return ActionInterface
     *
     * @throws InvalidIdentifierStringException
     * @throws InvalidActionTypeException
     * @throws MissingValueException
     */
    public function create(ActionDataInterface $actionData): ActionInterface
    {
        $type = (string) $actionData->getType();
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

        $valueData = $actionData->getValue();
        if (null === $valueData) {
            throw new MissingValueException();
        }

        $value = $this->valueFactory->createFromValueString((string) $valueData);

        return new InputAction($actionData->getSource(), $identifier, $value, (string) $actionData->getArguments());
    }
}
