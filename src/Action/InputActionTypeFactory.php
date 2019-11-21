<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilDataStructure\Action\InputAction as InputActionData;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;
use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;
use webignition\BasilModelFactory\IdentifierTypes;
use webignition\BasilModelFactory\ValueFactory;

class InputActionTypeFactory implements ActionTypeFactoryInterface
{
    const IDENTIFIER_STOP_WORD = ' to ';

    private $identifierFactory;
    private $identifierStringExtractor;
    private $valueFactory;

    public function __construct(
        IdentifierFactory $identifierFactory,
        IdentifierStringExtractor $identifierStringExtractor,
        ValueFactory $valueFactory
    ) {
        $this->identifierFactory = $identifierFactory;
        $this->identifierStringExtractor = $identifierStringExtractor;
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory(): InputActionTypeFactory
    {
        return new InputActionTypeFactory(
            IdentifierFactory::createFactory(),
            IdentifierStringExtractor::create(),
            ValueFactory::createFactory()
        );
    }

    public function handles(string $type): bool
    {
        return ActionTypes::SET === $type;
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
     * @throws MissingValueException
     */
    public function createForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        if (!$this->handles($type)) {
            throw new InvalidActionTypeException($type);
        }

        $identifierString = $this->identifierStringExtractor->extractFromStart($arguments);

        $identifier = $this->identifierFactory->create($identifierString, [
            IdentifierTypes::ELEMENT_REFERENCE,
            IdentifierTypes::ELEMENT_SELECTOR,
            IdentifierTypes::PAGE_ELEMENT_REFERENCE,
        ]);

        if (null === $identifier) {
            throw new InvalidIdentifierStringException($identifierString);
        }

        $trimmedStopWord = trim(self::IDENTIFIER_STOP_WORD);
        $endsWithStopStringRegex = '/(( ' . $trimmedStopWord . ' )|( ' . $trimmedStopWord . '))$/';

        if (preg_match($endsWithStopStringRegex, $arguments) > 0) {
            throw new MissingValueException();
        }

        if ($arguments === $identifierString) {
            throw new MissingValueException();
        }

        $keywordAndValueString = mb_substr($arguments, mb_strlen($identifierString));

        $stopWord = self::IDENTIFIER_STOP_WORD;
        $hasToKeyword = substr($keywordAndValueString, 0, strlen($stopWord)) === $stopWord;

        if ($hasToKeyword) {
            $valueString = mb_substr($keywordAndValueString, mb_strlen(self::IDENTIFIER_STOP_WORD));
            $value = $this->valueFactory->createFromValueString($valueString);
        } else {
            $value = $this->valueFactory->createFromValueString($keywordAndValueString);
        }

        return new InputAction($actionString, $identifier, $value, $arguments);
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
