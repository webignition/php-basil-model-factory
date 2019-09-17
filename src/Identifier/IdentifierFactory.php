<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\DomIdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;

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
            DomIdentifierFactory::createFactory(),
            DomReferenceIdentifierFactory::createFactory(),
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
        $identifier = $this->create($identifierString);

        if ($identifier instanceof IdentifierInterface) {
            $identifier = $identifier->withName($elementName);
        }

        if ($identifier instanceof DomIdentifierInterface && $parentIdentifier instanceof DomIdentifierInterface) {
            return $identifier->withParentIdentifier($parentIdentifier);
        }

        return $identifier;
    }

    /**
     * @param string $identifierString
     * @param array $allowedTypes
     *
     * @return IdentifierInterface|null
     */
    public function create(string $identifierString, array $allowedTypes = [
        IdentifierTypes::ATTRIBUTE_REFERENCE,
        IdentifierTypes::ATTRIBUTE_SELECTOR,
        IdentifierTypes::ELEMENT_REFERENCE,
        IdentifierTypes::ELEMENT_SELECTOR,
        IdentifierTypes::PAGE_ELEMENT_REFERENCE,
    ]): ?IdentifierInterface
    {
        $identifierString = trim($identifierString);
        $identifierTypeFactory = $this->findIdentifierTypeFactory($identifierString);

        if ($identifierTypeFactory instanceof IdentifierTypeFactoryInterface) {
            $identifier = $identifierTypeFactory->create($identifierString);

            if ($identifier instanceof IdentifierInterface) {
                $identifierType = IdentifierTypeFinder::findTypeFromIdentifier($identifier);

                return in_array($identifierType, $allowedTypes) ? $identifier : null;
            }
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
