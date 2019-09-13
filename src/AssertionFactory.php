<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Assertion\AssertableComparisonAssertion;
use webignition\BasilModel\Assertion\AssertableExaminationAssertion;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ComparisonAssertionInterface;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertionInterface;
use webignition\BasilModel\Exception\InvalidAssertionExaminedValueException;
use webignition\BasilModel\Exception\InvalidAssertionExpectedValueException;
use webignition\BasilModel\Identifier\AttributeIdentifierInterface;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Value\Assertion\AssertableExaminedValue;
use webignition\BasilModel\Value\Assertion\AssertableExpectedValue;
use webignition\BasilModel\Value\Assertion\ExaminedValue;
use webignition\BasilModel\Value\Assertion\ExpectedValue;
use webignition\BasilModel\Value\AttributeValue;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
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
     * @throws EmptyAssertionStringException
     * @throws MalformedPageElementReferenceException
     */
    public function createFromAssertionString(string $assertionString): AssertionInterface
    {
        $assertionString = trim($assertionString);
        if ('' === $assertionString) {
            throw new EmptyAssertionStringException();
        }

        $identifierString = $this->identifierStringExtractor->extractFromStart($assertionString);

        $examinedValue = $this->createExaminedValue($identifierString);

        $comparisonAndExpectedValue = trim(mb_substr($assertionString, mb_strlen($identifierString)));
        list($comparison, $expectedValueString) = $this->findComparisonAndExpectedValue($comparisonAndExpectedValue);

        if (in_array($comparison, AssertionComparison::EXAMINATION_COMPARISONS)) {
            return new ExaminationAssertion(
                $assertionString,
                $examinedValue,
                $comparison
            );
        }

        return new ComparisonAssertion(
            $assertionString,
            $examinedValue,
            $comparison,
            new ExpectedValue($this->valueFactory->createFromValueString($expectedValueString))
        );
    }

    /**
     * @param AssertionInterface $assertion
     *
     * @return AssertionInterface
     *
     * @throws InvalidAssertionExaminedValueException
     * @throws InvalidAssertionExpectedValueException
     */
    public function createAssertableAssertion(AssertionInterface $assertion): AssertionInterface
    {
        if (!($assertion instanceof ExaminationAssertionInterface ||
            $assertion instanceof ComparisonAssertionInterface)) {
            return $assertion;
        }

        if ($assertion instanceof ComparisonAssertionInterface) {
            return new AssertableComparisonAssertion(
                $assertion->getAssertionString(),
                new AssertableExaminedValue($assertion->getExaminedValue()->getExaminedValue()),
                $assertion->getComparison(),
                new AssertableExpectedValue($assertion->getExpectedValue()->getExpectedValue())
            );
        }

        return new AssertableExaminationAssertion(
            $assertion->getAssertionString(),
            new AssertableExaminedValue($assertion->getExaminedValue()->getExaminedValue()),
            $assertion->getComparison()
        );
    }

    private function findComparisonAndExpectedValue(string $comparisonAndExpectedValue): array
    {
        if (substr_count($comparisonAndExpectedValue, ' ') === 0) {
            return [
                $comparisonAndExpectedValue,
                ''
            ];
        }

        return explode(' ', $comparisonAndExpectedValue, 2);
    }

    /**
     * @param string $identifierString
     *
     * @return ExaminedValue
     *
     * @throws MalformedPageElementReferenceException
     */
    private function createExaminedValue(string $identifierString): ValueInterface
    {
        if (IdentifierTypeFinder::isElementIdentifier($identifierString)) {
            $elementIdentifier = $this->elementIdentifierFactory->create($identifierString);

            if ($elementIdentifier instanceof ElementIdentifierInterface) {
                return new ExaminedValue(
                    new ElementValue($elementIdentifier)
                );
            }
        }

        if (IdentifierTypeFinder::isAttributeReference($identifierString)) {
            $attributeIdentifier = $this->attributeIdentifierFactory->create($identifierString);

            if ($attributeIdentifier instanceof AttributeIdentifierInterface) {
                return new ExaminedValue(
                    new AttributeValue($attributeIdentifier)
                );
            }
        }

        return new ExaminedValue(
            $this->valueFactory->createFromValueString($identifierString)
        );
    }
}
