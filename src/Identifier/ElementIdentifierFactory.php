<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModelFactory\IdentifierTypeFinder;

class ElementIdentifierFactory implements IdentifierTypeFactoryInterface
{
    public static function createFactory(): ElementIdentifierFactory
    {
        return new ElementIdentifierFactory();
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::ELEMENT_SELECTOR === IdentifierTypeFinder::findType($identifierString);
    }

    public function create(string $identifierString, ?string $name = null): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);

        list($value, $position) = IdentifierStringValueAndPositionExtractor::extract($identifierString);
        $value = trim($value, '"');

        $value = IdentifierTypeFinder::isCssSelector($identifierString)
            ? LiteralValue::createCssSelectorValue($value)
            : LiteralValue::createXpathExpressionValue($value);

        $identifier = new ElementIdentifier($value, $position);

        if (null !== $name) {
            $identifier = $identifier->withName($name);
        }

        return $identifier;
    }
}
