<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;

class DomIdentifierFactory implements IdentifierTypeFactoryInterface
{
    public static function createFactory(): DomIdentifierFactory
    {
        return new DomIdentifierFactory();
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
        $elementExpressionAndPosition = $identifierString;
        $attributeName = '';

        $identifierType = IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);

        if (IdentifierTypes::ATTRIBUTE_SELECTOR === $identifierType) {
            list($elementExpressionAndPosition, $attributeName) = $this->extractAttributeNameAndElementIdentifier(
                $identifierString
            );
        }

        list($elementExpressionString, $position) = IdentifierStringValueAndPositionExtractor::extract(
            $elementExpressionAndPosition
        );

        $elementExpressionType = IdentifierTypeFinder::isCssSelector($elementExpressionString)
            ? ElementExpressionType::CSS_SELECTOR
            : ElementExpressionType::XPATH_EXPRESSION;

        $elementExpression = new ElementExpression(trim($elementExpressionString, '"'), $elementExpressionType);
        $identifier = new DomIdentifier($elementExpression);

        if (null !== $position) {
            $identifier = $identifier->withPosition($position);
        }

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
