<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Value\ElementValue;
use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;

class AssertionFactory
{
    private $identifierFactory;
    private $valueFactory;
    private $identifierStringExtractor;

    public function __construct(
        IdentifierFactory $identifierFactory,
        ValueFactory $valueFactory,
        IdentifierStringExtractor $identifierStringExtractor
    ) {
        $this->identifierFactory = $identifierFactory;
        $this->valueFactory = $valueFactory;
        $this->identifierStringExtractor = $identifierStringExtractor;
    }

    public static function createFactory(): AssertionFactory
    {
        return new AssertionFactory(
            IdentifierFactory::createFactory(),
            ValueFactory::createFactory(),
            IdentifierStringExtractor::create()
        );
    }

    /**
     * @param string $assertionString
     *
     * @return AssertionInterface
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

        if (IdentifierFactory::isIdentifier($identifierString)) {
            $examinedValue = new ElementValue($this->identifierFactory->create($identifierString));
        } else {
            $examinedValue = $this->valueFactory->createFromValueString($identifierString);
        }

        $comparisonAndExpectedValue = trim(mb_substr($assertionString, mb_strlen($identifierString)));

        if (substr_count($comparisonAndExpectedValue, ' ') === 0) {
            $comparison = $comparisonAndExpectedValue;
            $valueString = null;
        } else {
            $comparisonAndValueParts = explode(' ', $comparisonAndExpectedValue, 2);
            list($comparison, $valueString) = $comparisonAndValueParts;

            if (in_array($comparison, AssertionComparisons::NO_VALUE_TYPES)) {
                $expectedValue = null;
            } else {
                $expectedValue = $this->valueFactory->createFromValueString($valueString);
            }
        }

        return new Assertion($assertionString, $examinedValue, $comparison, $expectedValue);
    }
}
