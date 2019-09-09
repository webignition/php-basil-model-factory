<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\CssSelector;
use webignition\BasilModel\Value\XpathExpression;
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

    public function create(string $identifierString): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);

        list($elementExpression, $position) = IdentifierStringValueAndPositionExtractor::extract($identifierString);
        $elementExpression = trim($elementExpression, '"');

        $elementExpression = IdentifierTypeFinder::isCssSelector($identifierString)
            ? new CssSelector($elementExpression)
            : new XpathExpression($elementExpression);

        return new ElementIdentifier($elementExpression, $position);
    }
}
