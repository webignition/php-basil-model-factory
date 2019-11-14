<?php

namespace webignition\BasilModelFactory\IdentifierStringExtractor;

class VariableParameterIdentifierStringExtractor implements IdentifierStringTypeExtractorInterface
{
    const VARIABLE_START_CHARACTER = '$';

    public function handles(string $string): bool
    {
        return '' !== $string && self::VARIABLE_START_CHARACTER === $string[0];
    }

    public function extractFromStart(string $string): ?string
    {
        if (!$this->handles($string)) {
            return null;
        }

        return $this->parse($string);
    }

    private function parse(string $string): ?string
    {
        $defaultValueDelimiter = '|';

        $length = mb_strlen($string);
        $identifier = '';
        $isInDefaultValue = false;
        $previousCharacter = '';

        for ($i = 0; $i < $length; $i++) {
            $currentCharacter = mb_substr($string, $i, 1);

            if ($defaultValueDelimiter === $currentCharacter) {
                $isInDefaultValue = true;
            }

            if (false === $isInDefaultValue && ' ' === $currentCharacter) {
                return $identifier;
            }

            if (true === $isInDefaultValue && '" ' === $previousCharacter . $currentCharacter) {
                return $identifier;
            }

            $identifier .= $currentCharacter;
            $previousCharacter = $currentCharacter;
        }

        return $identifier;
    }
}
