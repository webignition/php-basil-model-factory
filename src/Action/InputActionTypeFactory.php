<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;
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

    public static function createFactory(): InputActionTypeFactory
    {
        return new InputActionTypeFactory(
            IdentifierFactory::createFactory(),
            IdentifierStringExtractor::create(),
            ValueFactory::createFactory()
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

        if (!in_array($identifier->getType(), [
            IdentifierTypes::ELEMENT_PARAMETER,
            IdentifierTypes::PAGE_ELEMENT_REFERENCE,
            IdentifierTypes::ELEMENT_SELECTOR,
        ])) {
            throw new MalformedPageElementReferenceException(
                new PageElementReference($identifierString)
            );
        }

//        var_dump($identifier);
//        exit();

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
