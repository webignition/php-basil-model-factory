<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\PageElementReference\PageElementReference as PageElementReferenceModel;
use webignition\BasilModel\Value\AttributeReference;
use webignition\BasilModel\Value\BrowserProperty;
use webignition\BasilModel\Value\CssSelector;
use webignition\BasilModel\Value\DataParameter;
use webignition\BasilModel\Value\ElementExpressionInterface;
use webignition\BasilModel\Value\ElementReference;
use webignition\BasilModel\Value\EnvironmentValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\PageElementReference as PageElementReferenceValue;
use webignition\BasilModel\Value\PageProperty;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModel\Value\XpathExpression;

class ValueFactory
{
    const OBJECT_PARAMETER_PREFIX = '$%s.';
    const QUOTED_STRING_PATTERN = '/^"[^"]+"$/';
    const ENVIRONMENT_PARAMETER_WITH_DEFAULT_PATTERN = '/^[^|]+\|/';
    const ENVIRONMENT_PARAMETER_DEFAULT_DELIMITER = '|';
    const ELEMENT_NAME_ATTRIBUTE_NAME_DELIMITER = '.';

    const OBJECT_NAME_VALUE_TYPE_MAP = [
        ValueTypes::TYPE_DATA_PARAMETER,
        ValueTypes::TYPE_ELEMENT_PARAMETER,
        ValueTypes::TYPE_PAGE_PROPERTY,
        ValueTypes::TYPE_BROWSER_PROPERTY,
        ValueTypes::TYPE_ENVIRONMENT_PARAMETER,
    ];

    public static function createFactory(): ValueFactory
    {
        return new ValueFactory();
    }

    public function createFromValueString(string $valueString): ValueInterface
    {
        $valueString = trim($valueString);

        if ('' === $valueString) {
            return new LiteralValue('');
        }

        $objectProperties = $this->findObjectProperties($valueString);

        if (is_array($objectProperties)) {
            list($objectName, $propertyName) = $objectProperties;

            return $this->createObjectValue($objectName, $valueString, $propertyName);
        }

        $isUnprocessedStringQuoted = preg_match(self::QUOTED_STRING_PATTERN, $valueString) === 1;

        $valueString = $this->getQuotedValue($valueString);

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

    public function createFromIdentifierString(string $identifierString): ?ElementExpressionInterface
    {
        $identifierString = trim($identifierString);

        if ('' === $identifierString) {
            return null;
        }

        if (IdentifierTypeFinder::isCssSelector($identifierString)) {
            return new CssSelector($this->getQuotedValue($identifierString));
        }

        if (IdentifierTypeFinder::isXpathExpression($identifierString)) {
            return new XpathExpression($this->getQuotedValue($identifierString));
        }

        return null;
    }

    private function findObjectProperties(string $valueString): ?array
    {
        foreach (self::OBJECT_NAME_VALUE_TYPE_MAP as $objectName) {
            $objectPrefix = sprintf(self::OBJECT_PARAMETER_PREFIX, $objectName);

            if (mb_strpos($valueString, $objectPrefix) === 0) {
                $propertyName = mb_substr($valueString, strlen($objectPrefix));

                if (ValueTypes::TYPE_ELEMENT_PARAMETER === $objectName &&
                    substr_count($propertyName, self::ELEMENT_NAME_ATTRIBUTE_NAME_DELIMITER) > 0) {
                    $objectName = ValueTypes::TYPE_ATTRIBUTE_PARAMETER;
                }

                return [
                    $objectName,
                    $propertyName,
                ];
            }
        }

        return null;
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

    private function createObjectValue(string $type, string $reference, string $value): ?ValueInterface
    {
        if (ValueTypes::TYPE_DATA_PARAMETER === $type) {
            return new DataParameter($reference, $value);
        }

        if (ValueTypes::TYPE_ELEMENT_PARAMETER === $type) {
            return new ElementReference($reference, $value);
        }

        if (ValueTypes::TYPE_ATTRIBUTE_PARAMETER === $type) {
            return new AttributeReference($reference, $value);
        }

        if (ValueTypes::TYPE_PAGE_PROPERTY === $type) {
            return new PageProperty($reference, $value);
        }

        if (ValueTypes::TYPE_BROWSER_PROPERTY === $type) {
            return new BrowserProperty($reference, $value);
        }

        if (ValueTypes::TYPE_ENVIRONMENT_PARAMETER === $type) {
            return $this->createEnvironmentValue($reference, $value);
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
}
