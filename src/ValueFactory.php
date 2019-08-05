<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModel\Value\EnvironmentValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\LiteralValueInterface;
use webignition\BasilModel\Value\ObjectNames;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModel\Value\ValueTypes;

class ValueFactory
{
    const OBJECT_PARAMETER_PREFIX = '$%s.';
    const QUOTED_STRING_PATTERN = '/^"[^"]+"$/';
    const ENVIRONMENT_PARAMETER_WITH_DEFAULT_PATTERN = '/^[^|]+\|/';
    const ENVIRONMENT_PARAMETER_DEFAULT_DELIMITER = '|';

    const OBJECT_NAME_VALUE_TYPE_MAP = [
        ObjectNames::DATA => ValueTypes::DATA_PARAMETER,
        ObjectNames::ELEMENT => ValueTypes::ELEMENT_PARAMETER,
        ObjectNames::PAGE => ValueTypes::PAGE_OBJECT_PROPERTY,
        ObjectNames::BROWSER => ValueTypes::BROWSER_OBJECT_PROPERTY,
        ObjectNames::ENVIRONMENT => ValueTypes::ENVIRONMENT_PARAMETER,
    ];

    const DATA_PARAMETER_PREFIX = '$data.';
    const ELEMENT_PARAMETER_PREFIX = '$elements.';
    const PAGE_OBJECT_PARAMETER_PREFIX = '$page.';
    const BROWSER_OBJECT_PARAMETER_PREFIX = '$browser.';

    public static function createFactory(): ValueFactory
    {
        return new ValueFactory();
    }

    public function createFromValueString(string $valueString): ValueInterface
    {
        $valueString = trim($valueString);

        if ('' === $valueString) {
            return LiteralValue::createStringValue('');
        }

        $objectProperties = $this->findObjectProperties($valueString);
        if (is_array($objectProperties)) {
            list($objectType, $objectName, $propertyName) = $objectProperties;

            if (ValueTypes::ENVIRONMENT_PARAMETER === $objectType) {
                return $this->createEnvironmentValue($valueString, $propertyName);
            }

            return new ObjectValue($objectType, $valueString, $objectName, $propertyName);
        }

        $isUnprocessedStringQuoted = preg_match('/^"[^"]+"$/', $valueString) === 1;

        $valueString = $this->getQuotedValue($valueString);

        $isDeQuotedStringQuoted = preg_match('/^"[^"]+"$/', $valueString) === 1;

        if (!$isUnprocessedStringQuoted && !$isDeQuotedStringQuoted) {
            $pageElementReference = new PageElementReference($valueString);

            if ($pageElementReference->isValid()) {
                return new ObjectValue(
                    ValueTypes::PAGE_ELEMENT_REFERENCE,
                    $valueString,
                    $pageElementReference->getImportName(),
                    $pageElementReference->getElementName()
                );
            }
        }

        return LiteralValue::createStringValue($valueString);
    }

    public function createFromIdentifierString(string $identifierString): LiteralValueInterface
    {
        $identifierString = trim($identifierString);

        if ('' === $identifierString) {
            return LiteralValue::createStringValue('');
        }

        if (IdentifierTypeFinder::isCssSelector($identifierString)) {
            return LiteralValue::createCssSelectorValue($this->getQuotedValue($identifierString));
        }

        if (IdentifierTypeFinder::isXpathExpression($identifierString)) {
            return LiteralValue::createXpathExpressionValue($this->getQuotedValue($identifierString));
        }

        return LiteralValue::createStringValue($identifierString);
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

    private function createEnvironmentValue(string $valueString, string $propertyName)
    {
        $default = null;

        if (preg_match(self::ENVIRONMENT_PARAMETER_WITH_DEFAULT_PATTERN, $propertyName)) {
            $propertyNameDefaultParts = explode(self::ENVIRONMENT_PARAMETER_DEFAULT_DELIMITER, $propertyName, 2);
            $propertyName = $propertyNameDefaultParts[0];
            $default = $this->getQuotedValue($propertyNameDefaultParts[1]);
        }

        return new EnvironmentValue($valueString, $propertyName, $default);
    }

    private function getQuotedValue(string $valueString): string
    {
        if ('' === $valueString) {
            return $valueString;
        }

        if ('"' === $valueString[0]) {
            $valueString = mb_substr($valueString, 1);
        }

        if ('"' === $valueString[-1]) {
            $valueString = mb_substr($valueString, 0, -1);
        }

        return str_replace('\\"', '"', $valueString);
    }
}
