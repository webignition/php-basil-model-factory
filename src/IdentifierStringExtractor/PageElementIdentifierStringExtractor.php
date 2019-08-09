<?php

namespace webignition\BasilModelFactory\IdentifierStringExtractor;

class PageElementIdentifierStringExtractor implements IdentifierStringTypeExtractorInterface
{
    const SELECTOR_DELIMITER = '"';
    const ESCAPED_SELECTOR_DELIMITER = '\\' . self::SELECTOR_DELIMITER;
    const POSITION_DELIMITER = ':';
    const ATTRIBUTE_NAME_DELIMITER = '.';
    const POSITION_FIRST = 'first';
    const POSITION_LAST = 'last';

    public function handles(string $string): bool
    {
        return '' !== $string && self::SELECTOR_DELIMITER === $string[0];
    }

    public function extractFromStart(string $string): ?string
    {
        if (!$this->handles($string)) {
            return null;
        }

        $selector = mb_substr($string, 0, $this->findEndingQuotePosition($string) + 1);
        $remainder = mb_substr($string, mb_strlen($selector));

        if ('' === $remainder) {
            return $selector;
        }

        $identifierString = $selector;

        if (self::POSITION_DELIMITER === $remainder[0]) {
            $remainder = mb_substr($remainder, 1);
            $position = $this->findPosition($remainder);

            if (is_string($position)) {
                $identifierString .= self::POSITION_DELIMITER . (string)$position;
                $remainder = mb_substr($remainder, strlen($position));
            }
        }

        if (self::ATTRIBUTE_NAME_DELIMITER === $remainder[0]) {
            $remainder = mb_substr($remainder, 1);
            $attributeName = $this->findAttributeName($remainder);

            if (null !== $attributeName) {
                $identifierString .= self::ATTRIBUTE_NAME_DELIMITER . $attributeName;
            }
        }

        return $identifierString;
    }

    private function findEndingQuotePosition(string $string): int
    {
        $currentQuotePosition = 0;
        $endingQuotePosition = null;

        while (null === $endingQuotePosition) {
            $nextQuotePosition = mb_strpos($string, self::SELECTOR_DELIMITER, $currentQuotePosition + 1);

            if (mb_substr($string, $nextQuotePosition - 1, 2) !== self::ESCAPED_SELECTOR_DELIMITER) {
                $endingQuotePosition = $nextQuotePosition;
            } else {
                $currentQuotePosition = mb_strpos($string, self::SELECTOR_DELIMITER, $nextQuotePosition + 1);
            }
        }

        return (int) $endingQuotePosition;
    }

    private function findPosition(string $string): ?string
    {
        $parts = explode(' ', $string);

        if (0 === count($parts)) {
            return null;
        }

        $positionContainer = $parts[0];
        $positionParts = explode(self::ATTRIBUTE_NAME_DELIMITER, $positionContainer);
        $position = $positionParts[0];

        if (self::POSITION_FIRST === $position) {
            return self::POSITION_FIRST;
        }

        if (self::POSITION_LAST === $position) {
            return self::POSITION_LAST;
        }

        $integerPosition = (int)$position;

        return (string)$integerPosition === $position ? $position : null;
    }

    private function findAttributeName(string $string): ?string
    {
        $parts = explode(' ', $string);

        if (0 === count($parts)) {
            return null;
        }

        return (string) $parts[0];
    }
}
