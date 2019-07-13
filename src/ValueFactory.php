<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\Value;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModel\Value\ValueTypes;

class ValueFactory
{
    const OBJECT_PARAMETER_PREFIX = '$%s.';

    const OBJECT_NAME_VALUE_TYPE_MAP = [
        'data' => ValueTypes::DATA_PARAMETER,
        'elements' => ValueTypes::ELEMENT_PARAMETER,
        'page' => ValueTypes::PAGE_OBJECT_PROPERTY,
        'browser' => ValueTypes::BROWSER_OBJECT_PROPERTY,
    ];

    const DATA_PARAMETER_PREFIX = '$data.';
    const ELEMENT_PARAMETER_PREFIX = '$elements.';
    const PAGE_OBJECT_PARAMETER_PREFIX = '$page.';
    const BROWSER_OBJECT_PARAMETER_PREFIX = '$browser.';

    public function createFromValueString(string $valueString): ValueInterface
    {
        $valueString = trim($valueString);
        $type = ValueTypes::STRING;

        if ('' === $valueString) {
            return new Value($type, '');
        }

        $objectProperties = $this->findObjectProperties($valueString);
        if (is_array($objectProperties)) {
            list($objectType, $objectName, $propertyName) = $objectProperties;

            return new ObjectValue($objectType, $valueString, $objectName, $propertyName);
        }

        if ('"' === $valueString[0]) {
            $valueString = mb_substr($valueString, 1);
        }

        if ('"' === $valueString[-1]) {
            $valueString = mb_substr($valueString, 0, -1);
        }

        $valueString = str_replace('\\"', '"', $valueString);

        return new Value($type, $valueString);
    }

    private function findObjectProperties(string $valueString): ?array
    {
        foreach (self::OBJECT_NAME_VALUE_TYPE_MAP as $objectName => $mappedType) {
            $objectPrefix = sprintf(self::OBJECT_PARAMETER_PREFIX, $objectName);

            if (mb_strpos($valueString, $objectPrefix) === 0) {
                $propertyName = mb_substr($valueString, strlen($objectPrefix));

                return [
                    $mappedType,
                    $objectName,
                    $propertyName,
                ];
            }
        }

        return null;
    }
}
