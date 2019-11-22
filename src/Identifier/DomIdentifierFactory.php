<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;
use webignition\BasilModelFactory\QuotedStringExtractor;

class DomIdentifierFactory implements IdentifierTypeFactoryInterface
{
    private $quotedStringExtractor;

    public function __construct(QuotedStringExtractor $quotedStringExtractor)
    {
        $this->quotedStringExtractor = $quotedStringExtractor;
    }

    public static function createFactory(): DomIdentifierFactory
    {
        return new DomIdentifierFactory(
            QuotedStringExtractor::createExtractor()
        );
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        $identifierType = IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);

        return in_array($identifierType, [
            IdentifierTypes::ELEMENT_SELECTOR,
            IdentifierTypes::ATTRIBUTE_SELECTOR
        ]);
    }

    public function create(string $identifierString): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);
        $elementLocatorAndPosition = $identifierString;
        $attributeName = '';

        $identifierType = IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);

        if (IdentifierTypes::ATTRIBUTE_SELECTOR === $identifierType) {
            list($elementLocatorAndPosition, $attributeName) = $this->extractAttributeNameAndElementIdentifier(
                $identifierString
            );
        }

        list($elementLocatorString, $position) = IdentifierStringValueAndPositionExtractor::extract(
            $elementLocatorAndPosition
        );

        $elementLocatorString = $this->quotedStringExtractor->getQuotedValue($elementLocatorString);

        $identifier = new DomIdentifier($elementLocatorString, $position);

        if ('' !== $attributeName) {
            $identifier = $identifier->withAttributeName($attributeName);
        }

        return $identifier;
    }

    private function extractAttributeNameAndElementIdentifier(string $identifier)
    {
        $lastDotPosition = (int) mb_strrpos($identifier, '.');

        $elementIdentifier = mb_substr($identifier, 0, $lastDotPosition);
        $attributeName = mb_substr($identifier, $lastDotPosition + 1);

        return [
            $elementIdentifier,
            $attributeName
        ];
    }
}
