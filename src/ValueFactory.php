<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\PageElementReference\PageElementReference as PageElementReferenceModel;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference as PageElementReferenceValue;
use webignition\BasilModel\Value\ValueInterface;

class ValueFactory
{
    const OBJECT_PARAMETER_PREFIX = '$%s.';
    const QUOTED_STRING_PATTERN = '/^"[^"]+"$/';
    const ENVIRONMENT_PARAMETER_WITH_DEFAULT_PATTERN = '/^[^|]+\|/';
    const ENVIRONMENT_PARAMETER_DEFAULT_DELIMITER = '|';
    const ELEMENT_NAME_ATTRIBUTE_NAME_DELIMITER = '.';

    const OBJECT_NAME_VALUE_TYPE = [
        ValueTypes::TYPE_DATA_PARAMETER => ObjectValueType::DATA_PARAMETER,
        ValueTypes::TYPE_PAGE_PROPERTY => ObjectValueType::PAGE_PROPERTY,
        ValueTypes::TYPE_BROWSER_PROPERTY => ObjectValueType::BROWSER_PROPERTY,
        ValueTypes::TYPE_ENVIRONMENT_PARAMETER => ObjectValueType::ENVIRONMENT_PARAMETER,
    ];

    private $quotedStringExtractor;

    public function __construct(QuotedStringExtractor $quotedStringExtractor)
    {
        $this->quotedStringExtractor = $quotedStringExtractor;
    }

    public static function createFactory(): ValueFactory
    {
        return new ValueFactory(
            QuotedStringExtractor::createExtractor()
        );
    }

    public function createFromValueString(string $valueString): ValueInterface
    {
        $valueString = trim($valueString);

        if ('' === $valueString) {
            return new LiteralValue('');
        }

        $objectValueProperties = $this->findObjectValueProperties($valueString);
        if (is_array($objectValueProperties)) {
            list($objectType, $property) = $objectValueProperties;

            if (ObjectValueType::ENVIRONMENT_PARAMETER === $objectType) {
                return $this->createEnvironmentValue($valueString, $property);
            }

            return new ObjectValue($objectType, $valueString, $property);
        }

        $domIdentifierReferenceProperties = $this->findDomIdentifierReferenceProperties($valueString);
        if (is_array($domIdentifierReferenceProperties)) {
            list($domIdentifierReferenceType, $property) = $domIdentifierReferenceProperties;

            return new DomIdentifierReference($domIdentifierReferenceType, $valueString, $property);
        }

        $isUnprocessedStringQuoted = preg_match(self::QUOTED_STRING_PATTERN, $valueString) === 1;

        $valueString = $this->quotedStringExtractor->getQuotedValue($valueString);

        $isDeQuotedStringQuoted = preg_match(self::QUOTED_STRING_PATTERN, $valueString) === 1;

        if (!$isUnprocessedStringQuoted && !$isDeQuotedStringQuoted) {
            $pageElementReference = new PageElementReferenceModel($valueString);

            if ($pageElementReference->isValid()) {
                return new PageElementReferenceValue(
                    $valueString,
                    $pageElementReference->getImportName(),
                    $pageElementReference->getElementName()
                );
            }
        }

        return new LiteralValue($valueString);
    }

    private function findObjectValueProperties(string $valueString): ?array
    {
        foreach (self::OBJECT_NAME_VALUE_TYPE as $objectName => $valueType) {
            $objectPrefix = sprintf(self::OBJECT_PARAMETER_PREFIX, $objectName);

            if (mb_strpos($valueString, $objectPrefix) === 0) {
                $propertyName = mb_substr($valueString, strlen($objectPrefix));

                return [
                    $valueType,
                    $propertyName,
                ];
            }
        }

        return null;
    }

    private function findDomIdentifierReferenceProperties(string $valueString): ?array
    {
        $objectName = ValueTypes::TYPE_ELEMENT_PARAMETER;
        $objectPrefix = sprintf(self::OBJECT_PARAMETER_PREFIX, $objectName);

        if (mb_strpos($valueString, $objectPrefix) === 0) {
            $propertyName = mb_substr($valueString, strlen($objectPrefix));
            $domIdentifierReferenceType = DomIdentifierReferenceType::ELEMENT;

            if (ValueTypes::TYPE_ELEMENT_PARAMETER === $objectName &&
                substr_count($propertyName, self::ELEMENT_NAME_ATTRIBUTE_NAME_DELIMITER) > 0) {
                $domIdentifierReferenceType = DomIdentifierReferenceType::ATTRIBUTE;
            }

            return [
                $domIdentifierReferenceType,
                $propertyName,
            ];
        }

        return null;
    }

    private function createEnvironmentValue(string $valueString, string $property)
    {
        $default = '';

        if (preg_match(self::ENVIRONMENT_PARAMETER_WITH_DEFAULT_PATTERN, $property)) {
            $propertyNameDefaultParts = explode(self::ENVIRONMENT_PARAMETER_DEFAULT_DELIMITER, $property, 2);
            $property = $propertyNameDefaultParts[0];
            $default = $this->quotedStringExtractor->getQuotedValue($propertyNameDefaultParts[1]);
        }

        return new ObjectValue(ObjectValueType::ENVIRONMENT_PARAMETER, $valueString, $property, $default);
    }
}
