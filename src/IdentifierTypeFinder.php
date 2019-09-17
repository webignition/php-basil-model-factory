<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Identifier\DomIdentifierInterface;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\ReferenceIdentifierInterface;
use webignition\BasilModel\Identifier\ReferenceIdentifierTypes;
use webignition\BasilModel\PageElementReference\PageElementReference;

class IdentifierTypeFinder
{
    const POSITION_PATTERN = ':(-?[0-9]+|first|last)';
    const ELEMENT_IDENTIFIER_STARTING_PATTERN = '^"';
    const ELEMENT_IDENTIFIER_ENDING_PATTERN = '("|' . self::POSITION_PATTERN . ')';
    const CSS_SELECTOR_STARTING_PATTERN = '((?!\/).).+';
    const XPATH_EXPRESSION_STARTING_PATTERN = '\/.+';

    const CSS_SELECTOR_REGEX =
        '/' . self::ELEMENT_IDENTIFIER_STARTING_PATTERN .
        self::CSS_SELECTOR_STARTING_PATTERN .
        self::ELEMENT_IDENTIFIER_ENDING_PATTERN .
        '$/';

    const XPATH_EXPRESSION_REGEX =
        '/' . self::ELEMENT_IDENTIFIER_STARTING_PATTERN .
        self::XPATH_EXPRESSION_STARTING_PATTERN .
        self::ELEMENT_IDENTIFIER_ENDING_PATTERN .
        '$/';

    const ATTRIBUTE_IDENTIFIER_REGEX =
        '/' . self::ELEMENT_IDENTIFIER_STARTING_PATTERN .
        '(' . self::CSS_SELECTOR_STARTING_PATTERN . ')|(' . self::XPATH_EXPRESSION_STARTING_PATTERN . ')' .
        self::ELEMENT_IDENTIFIER_ENDING_PATTERN .
        '\.(.+)' .
        '$/';

    const ELEMENT_REFERENCE_REGEX = '/^\$elements\.[^.]+$/';
    const ATTRIBUTE_REFERENCE_REGEX = '/^\$elements\.[^.]+\.[^.]+$/';

    public static function isCssSelector(string $identifierString): bool
    {
        return 1 === preg_match(self::CSS_SELECTOR_REGEX, $identifierString);
    }

    public static function isXpathExpression(string $identifierString): bool
    {
        return 1 === preg_match(self::XPATH_EXPRESSION_REGEX, $identifierString);
    }

    public static function isElementIdentifier(string $identifierString): bool
    {
        return self::isCssSelector($identifierString) || self::isXpathExpression($identifierString);
    }

    public static function isAttributeIdentifier(string $identifierString): bool
    {
        if (self::isElementIdentifier($identifierString)) {
            return false;
        }

        return 1 === preg_match(self::ATTRIBUTE_IDENTIFIER_REGEX, $identifierString);
    }

    public static function isElementReference(string $identifierString): bool
    {
        return 1 === preg_match(self::ELEMENT_REFERENCE_REGEX, $identifierString);
    }

    public static function isAttributeReference(string $identifierString): bool
    {
        return 1 === preg_match(self::ATTRIBUTE_REFERENCE_REGEX, $identifierString);
    }

    public static function findTypeFromIdentifierString(string $identifierString): ?string
    {
        if (self::isElementIdentifier($identifierString)) {
            return IdentifierTypes::ELEMENT_SELECTOR;
        }

        if (self::isElementReference($identifierString)) {
            return IdentifierTypes::ELEMENT_REFERENCE;
        }

        if (self::isAttributeIdentifier($identifierString)) {
            return IdentifierTypes::ATTRIBUTE_SELECTOR;
        }

        $pageElementReference = new PageElementReference($identifierString);

        if ($pageElementReference->isValid()) {
            return IdentifierTypes::PAGE_ELEMENT_REFERENCE;
        }

        return null;
    }

    public static function findTypeFromIdentifier(IdentifierInterface $identifier): string
    {
        if ($identifier instanceof DomIdentifierInterface) {
            if (null === $identifier->getAttributeName()) {
                return IdentifierTypes::ELEMENT_SELECTOR;
            }

            return IdentifierTypes::ATTRIBUTE_SELECTOR;
        }

        if ($identifier instanceof ReferenceIdentifierInterface) {
            $identifierType = $identifier->getType();

            if (ReferenceIdentifierTypes::ELEMENT_REFERENCE === $identifierType) {
                return IdentifierTypes::ELEMENT_REFERENCE;
            }

            if (ReferenceIdentifierTypes::ATTRIBUTE_REFERENCE === $identifierType) {
                return IdentifierTypes::ATTRIBUTE_REFERENCE;
            }
        }

        return IdentifierTypes::PAGE_ELEMENT_REFERENCE;
    }
}
