<?php

namespace webignition\BasilModelFactory\IdentifierStringExtractor;

class LiteralParameterIdentifierStringExtractor implements IdentifierStringTypeExtractorInterface
{
    const VARIABLE_START_CHARACTER = '$';

    public function handles(string $string): bool
    {
        if ('' === $string) {
            return false;
        }

        $firstCharacter = $string[0];

        return $firstCharacter !== '"' && $firstCharacter !== '$';
    }

    public function extractFromStart(string $string): ?string
    {
        if (!$this->handles($string)) {
            return null;
        }

        $spacePosition = mb_strpos($string, ' ');

        if (false === $spacePosition) {
            return $string;
        }

        return mb_substr($string, 0, $spacePosition);
    }
}
