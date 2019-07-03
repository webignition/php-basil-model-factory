<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Value\Value;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModel\Value\ValueTypes;

class ValueFactory
{
    const DATA_PARAMETER_PREFIX = '$data.';
    const ELEMENT_PARAMETER_PREFIX = '$elements.';

    public function createFromValueString(string $valueString): ValueInterface
    {
        $valueString = trim($valueString);
        $type = ValueTypes::STRING;

        if ('' === $valueString) {
            return new Value($type, '');
        }

        $hasDataParameterPrefix = mb_strpos($valueString, self::DATA_PARAMETER_PREFIX) === 0;
        $hasElementParameterPrefix = mb_strpos($valueString, self::ELEMENT_PARAMETER_PREFIX) === 0;

        if ($hasDataParameterPrefix) {
            $type = ValueTypes::DATA_PARAMETER;
        } elseif ($hasElementParameterPrefix) {
            $type = ValueTypes::ELEMENT_PARAMETER;
        } else {
            if ('"' === $valueString[0]) {
                $valueString = mb_substr($valueString, 1);
            }

            if ('"' === $valueString[-1]) {
                $valueString = mb_substr($valueString, 0, -1);
            }

            $valueString = str_replace('\\"', '"', $valueString);
        }

        return new Value($type, $valueString);
    }
}
