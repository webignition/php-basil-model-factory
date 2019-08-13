<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;

class IdentifierFactory
{
    const REFERENCED_ELEMENT_REGEX = '/^"{{.+/';
    const REFERENCED_ELEMENT_EXTRACTOR_REGEX = '/^".+?(?=(}}))}}/';

    /**
     * @var IdentifierTypeFactoryInterface[]
     */
    private $identifierTypeFactories = [];

    public function __construct(array $identifierTypeFactories = [])
    {
        foreach ($identifierTypeFactories as $identifierTypeFactory) {
            if ($identifierTypeFactory instanceof IdentifierTypeFactoryInterface) {
                $this->addIdentifierTypeFactory($identifierTypeFactory);
            }
        }
    }

    public static function createFactory()
    {
        return new IdentifierFactory([
            ElementIdentifierFactory::createFactory(),
            AttributeIdentifierFactory::createFactory(),
            ElementParameterIdentifierFactory::createFactory(),
            PageElementReferenceIdentifierFactory::createFactory(),
        ]);
    }

    public function addIdentifierTypeFactory(IdentifierTypeFactoryInterface $identifierTypeFactory)
    {
        $this->identifierTypeFactories[] = $identifierTypeFactory;
    }

    /**
     * @param string $identifierString
     * @param string $elementName
     * @param IdentifierInterface[] $existingIdentifiers
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function createWithElementReference(
        string $identifierString,
        string $elementName,
        array $existingIdentifiers
    ): ?IdentifierInterface {
        $identifierString = trim($identifierString);

        if (empty($identifierString)) {
            return null;
        }

        $parentIdentifierName = null;

        if (1 === preg_match(self::REFERENCED_ELEMENT_REGEX, $identifierString)) {
            list($parentIdentifierName, $identifierString) =
                $this->extractElementReferenceAndIdentifierString($identifierString);
        }

        $parentIdentifier = $existingIdentifiers[$parentIdentifierName] ?? null;
        $identifier = $this->create($identifierString, $elementName);

        if ($identifier instanceof ElementIdentifierInterface &&
            $parentIdentifier instanceof ElementIdentifierInterface) {
            return $identifier->withParentIdentifier($parentIdentifier);
        }

        return $identifier;
    }

    /**
     * @param string $identifierString
     * @param string|null $name
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function create(
        string $identifierString,
        ?string $name = null
    ): ?IdentifierInterface {
        $identifierString = trim($identifierString);

        if (empty($identifierString)) {
            return null;
        }

        $identifierTypeFactory = $this->findIdentifierTypeFactory($identifierString);

        if ($identifierTypeFactory instanceof IdentifierTypeFactoryInterface) {
            return $identifierTypeFactory->create($identifierString, $name);
        }

        return null;
    }

    private function findIdentifierTypeFactory(string $identifierString): ?IdentifierTypeFactoryInterface
    {
        foreach ($this->identifierTypeFactories as $identifierTypeFactory) {
            if ($identifierTypeFactory->handles($identifierString)) {
                return $identifierTypeFactory;
            }
        }

        return null;
    }

    private function extractElementReferenceAndIdentifierString(string $identifier)
    {
        $elementReferenceMatches = [];
        preg_match(self::REFERENCED_ELEMENT_EXTRACTOR_REGEX, $identifier, $elementReferenceMatches);

        $elementReferencePart = $elementReferenceMatches[0];
        $identifierStringPart = trim(mb_substr($identifier, mb_strlen($elementReferencePart)));

        $elementReference = $elementReferencePart;

        if ('"' === $elementReference[0]) {
            $elementReference = ltrim($elementReference, '"');
        }

        $elementReference = trim($elementReference, '{} ');

        list($identifierString, $position) = IdentifierStringValueAndPositionExtractor::extract($identifierStringPart);

        if ('"' === $identifierString[-1] && '"' !== $identifierString[0]) {
            $identifierString = '"' . $identifierString;
        }

        if ($position) {
            $identifierString .= ':' . $position;
        }

        return [
            $elementReference,
            $identifierString
        ];
    }
}
