<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModelFactory\IdentifierFactory;
use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
use webignition\BasilModelFactory\ValueFactory;

class InputActionTypeFactory extends AbstractActionTypeFactory implements ActionTypeFactoryInterface
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

    public static function createFactory(
        ?IdentifierFactory $identifierFactory = null,
        ?IdentifierStringExtractor $identifierStringExtractor = null,
        ?ValueFactory $valueFactory = null
    ): InputActionTypeFactory {
        return new InputActionTypeFactory(
            $identifierFactory ?? IdentifierFactory::createFactory(),
            $identifierStringExtractor ?? IdentifierStringExtractor::create(),
            $valueFactory ?? ValueFactory::createFactory()
        );
    }

    protected function getHandledActionTypes(): array
    {
        return [
            ActionTypes::SET,
        ];
    }

    /**
     * @param string $actionString
     * @param string $type
     * @param string $arguments
     *
     * @return ActionInterface
     *
     * @throws MalformedPageElementReferenceException
     */
    protected function doCreateForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        $identifierString = $this->identifierStringExtractor->extractFromStart($arguments);

        if ('' === $identifierString) {
            return new InputAction($actionString, null, null, $arguments);
        }

        $identifier = $this->identifierFactory->create($identifierString);

        $trimmedStopWord = trim(self::IDENTIFIER_STOP_WORD);
        $endsWithStopStringRegex = '/(( ' . $trimmedStopWord . ' )|( ' . $trimmedStopWord . '))$/';

        if (preg_match($endsWithStopStringRegex, $arguments) > 0) {
            return new InputAction($actionString, $identifier, null, $arguments);
        }

        if ($arguments === $identifierString) {
            return new InputAction($actionString, $identifier, null, $arguments);
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
}
