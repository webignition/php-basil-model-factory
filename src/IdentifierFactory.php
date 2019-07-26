<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModel\Value\LiteralValue;

class IdentifierFactory
{
    const POSITION_FIRST = 'first';
    const POSITION_LAST = 'last';

    const POSITION_PATTERN = ':(-?[0-9]+|first|last)';
    const POSITION_REGEX = '/' . self::POSITION_PATTERN . '$/';
    const CSS_SELECTOR_REGEX = '/^"((?!\/).).+("|' . self::POSITION_PATTERN . ')$/';
    const XPATH_EXPRESSION_REGEX = '/^"\/.+("|' . self::POSITION_PATTERN . ')$/';
    const DATA_PARAMETER_REGEX = '/^\$data\.+/';
    const ELEMENT_PARAMETER_REGEX = '/^\$elements\.+/';
    const PAGE_OBJECT_PARAMETER_REGEX = '/^\$page\.+/';
    const BROWSER_OBJECT_PARAMETER_REGEX = '/^\$browser\.+/';
    const REFERENCED_ELEMENT_REGEX = '/^"{{.+/';
    const REFERENCED_ELEMENT_EXTRACTOR_REGEX = '/^".+?(?=(}}))}}/';

    private $valueFactory;

    public function __construct(ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory()
    {
        return new IdentifierFactory(
            ValueFactory::createFactory()
        );
    }

    /**
     * @param string $identifierString
     * @param string $elementName
     * @param IdentifierInterface[] $existingIdentifiers
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function createWithElementReference(
        string $identifierString,
        string $elementName,
        array $existingIdentifiers
    ): ?IdentifierInterface {
        $identifierString = trim($identifierString);

        if (empty($identifierString)) {
            return null;
        }

        $parentIdentifierName = null;

        if (1 === preg_match(self::REFERENCED_ELEMENT_REGEX, $identifierString)) {
            list($parentIdentifierName, $identifierString) =
                $this->extractElementReferenceAndIdentifierString($identifierString);
        }

        $parentIdentifier = $existingIdentifiers[$parentIdentifierName] ?? null;
        $identifier = $this->create($identifierString, $elementName);

        if ($identifier instanceof ElementIdentifierInterface &&
            $parentIdentifier instanceof ElementIdentifierInterface) {
            return $identifier->withParentIdentifier($parentIdentifier);
        }

        return $identifier;
    }

    /**
     * @param string $identifierString
     * @param string|null $name
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function create(
        string $identifierString,
        ?string $name = null
    ): ?IdentifierInterface {
        $identifierString = trim($identifierString);

        if (empty($identifierString)) {
            return null;
        }

        $type = $this->deriveType($identifierString);

        list($value, $position) = $this->extractValueAndPosition($identifierString);
        $value = trim($value, '"');

        if (IdentifierTypes::ELEMENT_SELECTOR === $type) {
            $value = IdentifierFactory::isCssSelector($identifierString)
                ? LiteralValue::createCssSelectorValue($value)
                : LiteralValue::createXpathExpressionValue($value);

            return new ElementIdentifier($value, $position, $name);
        }

        if (IdentifierTypes::PAGE_ELEMENT_REFERENCE === $type) {
            $pageElementReference = new PageElementReference($identifierString);

            if (!$pageElementReference->isValid()) {
                throw new MalformedPageElementReferenceException($pageElementReference);
            }
        }

        $referenceIdentifier = new Identifier($type, $this->valueFactory->createFromValueString($identifierString));

        if (null !== $name) {
            $referenceIdentifier = $referenceIdentifier->withName($name);
        }

        return $referenceIdentifier;
    }

    public static function isCssSelector(string $identifierString): bool
    {
        return 1 === preg_match(self::CSS_SELECTOR_REGEX, $identifierString);
    }

    public static function isXpathExpression(string $identifierString): bool
    {
        return 1 === preg_match(self::XPATH_EXPRESSION_REGEX, $identifierString);
    }

    public static function isElementIdentifier(string $identifierString): bool
    {
        return self::isCssSelector($identifierString) || self::isXpathExpression($identifierString);
    }

    public static function isElementParameterReference(string $identifierString): bool
    {
        return 1 === preg_match(self::ELEMENT_PARAMETER_REGEX, $identifierString);
    }

    private function deriveType(string $identifierString): string
    {
        if (self::isElementIdentifier($identifierString)) {
            return IdentifierTypes::ELEMENT_SELECTOR;
        }

        if (self::isElementParameterReference($identifierString)) {
            return IdentifierTypes::ELEMENT_PARAMETER;
        }

        return IdentifierTypes::PAGE_ELEMENT_REFERENCE;
    }

    private function extractValueAndPosition(string $identifier)
    {
        $positionMatches = [];

        preg_match(self::POSITION_REGEX, $identifier, $positionMatches);

        $position = 1;

        if (empty($positionMatches)) {
            $quotedValue = $identifier;
        } else {
            $quotedValue = (string) preg_replace(self::POSITION_REGEX, '', $identifier);

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

    private function extractElementReferenceAndIdentifierString(string $identifier)
    {
        $elementReferenceMatches = [];
        preg_match(self::REFERENCED_ELEMENT_EXTRACTOR_REGEX, $identifier, $elementReferenceMatches);

        $elementReferencePart = $elementReferenceMatches[0];
        $identifierStringPart = trim(mb_substr($identifier, mb_strlen($elementReferencePart)));

        $elementReference = $elementReferencePart;

        if ('"' === $elementReference[0]) {
            $elementReference = ltrim($elementReference, '"');
        }

        $elementReference = trim($elementReference, '{} ');

        $identifierString = $identifierStringPart;
        $position = null;

        if (preg_match(self::POSITION_REGEX, $identifierString)) {
            list($identifierString, $position) = $this->extractValueAndPosition($identifierString);
        }

        if ('"' === $identifierString[-1] && '"' !== $identifierString[0]) {
            $identifierString = '"' . $identifierString;
        }

        if ($position) {
            $identifierString .= ':' . $position;
        }

        return [
            $elementReference,
            $identifierString
        ];
    }
}
