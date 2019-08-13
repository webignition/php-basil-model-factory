<?php

namespace webignition\BasilModelFactory\Identifier;

class IdentifierStringValueAndPositionExtractor
{
    const POSITION_FIRST = 'first';
    const POSITION_LAST = 'last';
    const POSITION_PATTERN = ':(-?[0-9]+|first|last)';
    const POSITION_REGEX = '/' . self::POSITION_PATTERN . '$/';

    public static function extract(string $identifierString)
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
