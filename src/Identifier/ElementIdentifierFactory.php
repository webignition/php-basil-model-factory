<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModelFactory\IdentifierTypeFinder;

class ElementIdentifierFactory implements IdentifierTypeFactoryInterface
{
    const POSITION_FIRST = 'first';
    const POSITION_LAST = 'last';
    const POSITION_PATTERN = ':(-?[0-9]+|first|last)';
    const POSITION_REGEX = '/' . self::POSITION_PATTERN . '$/';

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

        list($value, $position) = $this->extractValueAndPosition($identifierString);
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

    private function extractValueAndPosition(string $identifierString)
    {
        $positionMatches = [];

        preg_match(self::POSITION_REGEX, $identifierString, $positionMatches);

        $position = 1;

        if (empty($positionMatches)) {
            $quotedValue = $identifierString;
        } else {
            $quotedValue = (string) preg_replace(self::POSITION_REGEX, '', $identifierString);

            $positionMatch = $positionMatches[0];
            $positionString = ltrim($positionMatch, ':');

            if (self::POSITION_FIRST === $positionString) {
                $position = 1;
            } elseif (self::POSITION_LAST === $positionString) {
                $position = -1;
            } else {
                $position = (int) $positionString;
            }
        }

        return [
            $quotedValue,
            $position,
        ];
    }
}
