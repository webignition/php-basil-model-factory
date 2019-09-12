<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ExcludesAssertion;
use webignition\BasilModel\Assertion\ExistsAssertion;
use webignition\BasilModel\Assertion\IncludesAssertion;
use webignition\BasilModel\Assertion\IsAssertion;
use webignition\BasilModel\Assertion\IsNotAssertion;
use webignition\BasilModel\Assertion\MatchesAssertion;
use webignition\BasilModel\Assertion\NotExistsAssertion;
use webignition\BasilModel\Assertion\ValueComparisonAssertionInterface;
use webignition\BasilModel\Identifier\AttributeIdentifierInterface;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Value\AssertionExaminedValue;
use webignition\BasilModel\Value\AssertionExpectedValue;
use webignition\BasilModel\Value\AttributeValue;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\InvalidComparisonException;
use webignition\BasilModelFactory\Identifier\AttributeIdentifierFactory;
use webignition\BasilModelFactory\Identifier\ElementIdentifierFactory;
use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;

class AssertionFactory
{
    private $valueFactory;
    private $identifierStringExtractor;
    private $elementIdentifierFactory;
    private $attributeIdentifierFactory;

    const EXISTENCE_COMPARISONS = [
        AssertionComparison::EXISTS,
        AssertionComparison::NOT_EXISTS,
    ];

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
     * @throws InvalidComparisonException
     * @throws MalformedPageElementReferenceException
     */
    public function createFromAssertionString(string $assertionString): AssertionInterface
    {
        $assertionString = trim($assertionString);
        if ('' === $assertionString) {
            throw new EmptyAssertionStringException();
        }

        $identifierString = $this->identifierStringExtractor->extractFromStart($assertionString);

        $expectedValue = null;
        $examinedValue = $this->createExaminedValue($identifierString);

        $comparisonAndExpectedValue = trim(mb_substr($assertionString, mb_strlen($identifierString)));
        list($comparison, $expectedValueString) = $this->findComparisonAndExpectedValue($comparisonAndExpectedValue);

        if (in_array($comparison, self::EXISTENCE_COMPARISONS)) {
            return $this->createExistenceAssertion($comparison, $assertionString, $examinedValue);
        }

        return $this->createValueComparisonAssertion(
            $comparison,
            $assertionString,
            $examinedValue,
            new AssertionExpectedValue($this->valueFactory->createFromValueString($expectedValueString))
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
     * @return AssertionExaminedValue
     *
     * @throws MalformedPageElementReferenceException
     */
    private function createExaminedValue(string $identifierString): ValueInterface
    {
        if (IdentifierTypeFinder::isElementIdentifier($identifierString)) {
            $elementIdentifier = $this->elementIdentifierFactory->create($identifierString);

            if ($elementIdentifier instanceof ElementIdentifierInterface) {
                return new AssertionExaminedValue(
                    new ElementValue($elementIdentifier)
                );
            }
        }

        if (IdentifierTypeFinder::isAttributeReference($identifierString)) {
            $attributeIdentifier = $this->attributeIdentifierFactory->create($identifierString);

            if ($attributeIdentifier instanceof AttributeIdentifierInterface) {
                return new AssertionExaminedValue(
                    new AttributeValue($attributeIdentifier)
                );
            }
        }

        return new AssertionExaminedValue(
            $this->valueFactory->createFromValueString($identifierString)
        );
    }

    /**
     * @param string $comparison
     * @param string $assertionString
     * @param AssertionExaminedValue $examinedValue
     *
     * @return AssertionInterface
     */
    private function createExistenceAssertion(
        string $comparison,
        string $assertionString,
        AssertionExaminedValue $examinedValue
    ): AssertionInterface {
        return AssertionComparison::EXISTS === $comparison
            ? new ExistsAssertion($assertionString, $examinedValue)
            : new NotExistsAssertion($assertionString, $examinedValue);
    }

    /**
     * @param string $comparison
     * @param string $assertionString
     * @param AssertionExaminedValue $examinedValue
     * @param AssertionExpectedValue $expectedValue
     *
     * @return ValueComparisonAssertionInterface
     *
     * @throws InvalidComparisonException
     */
    private function createValueComparisonAssertion(
        string $comparison,
        string $assertionString,
        AssertionExaminedValue $examinedValue,
        AssertionExpectedValue $expectedValue
    ): ValueComparisonAssertionInterface {
        if (AssertionComparison::IS === $comparison) {
            return new IsAssertion($assertionString, $examinedValue, $expectedValue);
        }

        if (AssertionComparison::IS_NOT === $comparison) {
            return new IsNotAssertion($assertionString, $examinedValue, $expectedValue);
        }

        if (AssertionComparison::INCLUDES === $comparison) {
            return new IncludesAssertion($assertionString, $examinedValue, $expectedValue);
        }

        if (AssertionComparison::EXCLUDES === $comparison) {
            return new ExcludesAssertion($assertionString, $examinedValue, $expectedValue);
        }

        if (AssertionComparison::MATCHES === $comparison) {
            return new MatchesAssertion($assertionString, $examinedValue, $expectedValue);
        }

        throw new InvalidComparisonException($assertionString, $comparison);
    }
}
