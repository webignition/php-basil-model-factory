<?php

namespace webignition\BasilModelFactory;

use webignition\BasilDataStructure\AssertionInterface as AssertionDataInterface;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\MissingComparisonException;
use webignition\BasilModelFactory\Exception\MissingValueException;

class AssertionFactory
{
    private $valueFactory;
    private $examinedValueFactory;

    public function __construct(
        ValueFactory $valueFactory,
        AssertionExaminedValueFactory $examinedValueFactory
    ) {
        $this->valueFactory = $valueFactory;
        $this->examinedValueFactory = $examinedValueFactory;
    }

    public static function createFactory(): AssertionFactory
    {
        return new AssertionFactory(
            ValueFactory::createFactory(),
            AssertionExaminedValueFactory::createFactory()
        );
    }

    /**
     * @param AssertionDataInterface $assertionData
     *
     * @return AssertionInterface
     *
     * @throws EmptyAssertionStringException
     * @throws MissingValueException
     * @throws MissingComparisonException
     */
    public function createFromAssertionData(AssertionDataInterface $assertionData): AssertionInterface
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
}
