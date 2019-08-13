<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Identifier\AttributeIdentifierInterface;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Value\AttributeValue;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModelFactory\Identifier\AttributeIdentifierFactory;
use webignition\BasilModelFactory\Identifier\ElementIdentifierFactory;
use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;

class AssertionFactory
{
    private $valueFactory;
    private $identifierStringExtractor;
    private $elementIdentifierFactory;
    private $attributeIdentifierFactory;

    public function __construct(
        ValueFactory $valueFactory,
        IdentifierStringExtractor $identifierStringExtractor,
        ElementIdentifierFactory $elementIdentifierFactory,
        AttributeIdentifierFactory $attributeIdentifierFactory
    ) {
        $this->valueFactory = $valueFactory;
        $this->identifierStringExtractor = $identifierStringExtractor;
        $this->elementIdentifierFactory = $elementIdentifierFactory;
        $this->attributeIdentifierFactory = $attributeIdentifierFactory;
    }

    public static function createFactory(): AssertionFactory
    {
        return new AssertionFactory(
            ValueFactory::createFactory(),
            IdentifierStringExtractor::create(),
            ElementIdentifierFactory::createFactory(),
            AttributeIdentifierFactory::createFactory()
        );
    }

    /**
     * @param string $assertionString
     *
     * @return AssertionInterface
     *
     * @throws MalformedPageElementReferenceException
     */
    public function createFromAssertionString(string $assertionString): AssertionInterface
    {
        $assertionString = trim($assertionString);
        if ('' === $assertionString) {
            return new Assertion('', null, null);
        }

        $identifierString = $this->identifierStringExtractor->extractFromStart($assertionString);

        $examinedValue = null;
        $expectedValue = null;

        if (IdentifierTypeFinder::isElementIdentifier($identifierString)) {
            $elementIdentifier = $this->elementIdentifierFactory->create($identifierString);

            if ($elementIdentifier instanceof ElementIdentifierInterface) {
                $examinedValue = new ElementValue($elementIdentifier);
            }
        }

        if (null === $examinedValue && IdentifierTypeFinder::isAttributeIdentifier($identifierString)) {
            $attributeIdentifier = $this->attributeIdentifierFactory->create($identifierString);

            if ($attributeIdentifier instanceof AttributeIdentifierInterface) {
                $examinedValue = new AttributeValue($attributeIdentifier);
            }
        }

        if (null === $examinedValue) {
            $examinedValue = $this->valueFactory->createFromValueString($identifierString);
        }

        $comparisonAndExpectedValue = trim(mb_substr($assertionString, mb_strlen($identifierString)));

        if (substr_count($comparisonAndExpectedValue, ' ') === 0) {
            $comparison = $comparisonAndExpectedValue;
            $valueString = null;
        } else {
            $comparisonAndValueParts = explode(' ', $comparisonAndExpectedValue, 2);
            list($comparison, $valueString) = $comparisonAndValueParts;

            if (!in_array($comparison, AssertionComparisons::NO_VALUE_TYPES)) {
                $expectedValue = $this->valueFactory->createFromValueString($valueString);
            }
        }

        return new Assertion($assertionString, $examinedValue, $comparison, $expectedValue);
    }
}
