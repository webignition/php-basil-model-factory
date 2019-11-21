<?php

namespace webignition\BasilModelFactory;

use webignition\BasilDataStructure\Assertion as AssertionData;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\MissingComparisonException;
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;

class AssertionFactory
{
    private $valueFactory;
    private $identifierStringExtractor;
    private $examinedValueFactory;

    public function __construct(
        ValueFactory $valueFactory,
        IdentifierStringExtractor $identifierStringExtractor,
        AssertionExaminedValueFactory $examinedValueFactory
    ) {
        $this->valueFactory = $valueFactory;
        $this->identifierStringExtractor = $identifierStringExtractor;
        $this->examinedValueFactory = $examinedValueFactory;
    }

    public static function createFactory(): AssertionFactory
    {
        return new AssertionFactory(
            ValueFactory::createFactory(),
            IdentifierStringExtractor::create(),
            AssertionExaminedValueFactory::createFactory()
        );
    }

    /**
     * @param string $assertionString
     *
     * @return AssertionInterface
     *
     * @throws EmptyAssertionStringException
     * @throws MissingValueException
     */
    public function createFromAssertionString(string $assertionString): AssertionInterface
    {
        $assertionString = trim($assertionString);
        if ('' === $assertionString) {
            throw new EmptyAssertionStringException();
        }

        $identifierString = $this->identifierStringExtractor->extractFromStart($assertionString);

        $examinedValue = $this->examinedValueFactory->create($identifierString);

        $comparisonAndExpectedValue = trim(mb_substr($assertionString, mb_strlen($identifierString)));
        list($comparison, $expectedValueString) = $this->findComparisonAndExpectedValue($comparisonAndExpectedValue);

        if (in_array($comparison, AssertionComparison::EXAMINATION_COMPARISONS)) {
            return new ExaminationAssertion(
                $assertionString,
                $examinedValue,
                $comparison
            );
        }

        if ('' === $expectedValueString) {
            throw new MissingValueException();
        }

        return new ComparisonAssertion(
            $assertionString,
            $examinedValue,
            $comparison,
            $this->valueFactory->createFromValueString($expectedValueString)
        );
    }

    /**
     * @param AssertionData $assertionData
     *
     * @return AssertionInterface
     *
     * @throws EmptyAssertionStringException
     * @throws MissingValueException
     * @throws MissingComparisonException
     */
    public function createFromAssertionData(AssertionData $assertionData): AssertionInterface
    {
        $source = $assertionData->getSource();
        if ('' === $source) {
            throw new EmptyAssertionStringException();
        }

        $comparison = (string) $assertionData->getComparison();
        if ('' === $comparison) {
            throw new MissingComparisonException();
        }

        $examinedValue = $this->examinedValueFactory->create((string) $assertionData->getIdentifier());

        if (in_array($comparison, AssertionComparison::EXAMINATION_COMPARISONS)) {
            return new ExaminationAssertion(
                $source,
                $examinedValue,
                $comparison
            );
        }

        $expectedValueString = $assertionData->getValue();
        if (null === $expectedValueString) {
            throw new MissingValueException();
        }

        return new ComparisonAssertion(
            $source,
            $examinedValue,
            $comparison,
            $this->valueFactory->createFromValueString($expectedValueString)
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
}
