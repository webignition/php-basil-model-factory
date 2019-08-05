<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Identifier\IdentifierTypes;

class IdentifierTypeFinder
{
    const POSITION_PATTERN = ':(-?[0-9]+|first|last)';
    const CSS_SELECTOR_REGEX = '/^"((?!\/).).+("|' . self::POSITION_PATTERN . ')$/';
    const XPATH_EXPRESSION_REGEX = '/^"\/.+("|' . self::POSITION_PATTERN . ')$/';
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

    public static function isElementParameterReference(string $identifierString): bool
    {
        return 1 === preg_match(self::ELEMENT_PARAMETER_REGEX, $identifierString);
    }

    public static function findType(string $identifierString): string
    {
        if (IdentifierTypeFinder::isElementIdentifier($identifierString)) {
            return IdentifierTypes::ELEMENT_SELECTOR;
        }

        if (IdentifierTypeFinder::isElementParameterReference($identifierString)) {
            return IdentifierTypes::ELEMENT_PARAMETER;
        }

        return IdentifierTypes::PAGE_ELEMENT_REFERENCE;
    }
}
