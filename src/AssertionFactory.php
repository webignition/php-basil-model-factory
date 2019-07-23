<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Assertion\Assertion;
use webignition\BasilModel\Assertion\AssertionComparisons;
use webignition\BasilModel\Assertion\AssertionInterface;
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
     *
     * @throws MalformedPageElementReferenceException
     */
    public function createFromAssertionString(string $assertionString): AssertionInterface
    {
        $identifierString = $this->identifierStringExtractor->extractFromStart($assertionString);

        $identifier = $this->identifierFactory->create($identifierString);
        $value = null;

        $comparisonAndValue = trim(mb_substr($assertionString, mb_strlen($identifierString)));

        if (substr_count($comparisonAndValue, ' ') === 0) {
            $comparison = $comparisonAndValue;
            $valueString = null;
        } else {
            $comparisonAndValueParts = explode(' ', $comparisonAndValue, 2);
            list($comparison, $valueString) = $comparisonAndValueParts;

            if (in_array($comparison, AssertionComparisons::NO_VALUE_TYPES)) {
                $value = null;
            } else {
                $value = $this->valueFactory->createFromValueString($valueString);
            }
        }

        return new Assertion($assertionString, $identifier, $comparison, $value);
    }
}
