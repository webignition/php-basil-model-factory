<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Identifier\IdentifierTypes;

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

    const ELEMENT_PARAMETER_REGEX = '/^\$elements\.+/';

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
        return 1 === preg_match(self::ATTRIBUTE_IDENTIFIER_REGEX, $identifierString);
    }

    public static function isElementParameterReference(string $identifierString): bool
    {
        return 1 === preg_match(self::ELEMENT_PARAMETER_REGEX, $identifierString);
    }

    public static function findType(string $identifierString): string
    {
        if (self::isElementIdentifier($identifierString)) {
            return IdentifierTypes::ELEMENT_SELECTOR;
        }

        if (self::isElementParameterReference($identifierString)) {
            return IdentifierTypes::ELEMENT_PARAMETER;
        }

        if (self::isAttributeIdentifier($identifierString)) {
            return IdentifierTypes::ATTRIBUTE;
        }

        return IdentifierTypes::PAGE_ELEMENT_REFERENCE;
    }
}