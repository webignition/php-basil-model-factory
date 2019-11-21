<?php

namespace webignition\BasilModelFactory;

use webignition\BasilModel\Identifier\DomIdentifierInterface;
use webignition\BasilModel\Value\DomIdentifierValue;
use webignition\BasilModel\Value\ValueInterface;
use webignition\BasilModelFactory\Identifier\DomIdentifierFactory;

class AssertionExaminedValueFactory
{
    private $domIdentifierFactory;
    private $valueFactory;

    public function __construct(DomIdentifierFactory $domIdentifierFactory, ValueFactory $valueFactory)
    {
        $this->domIdentifierFactory = $domIdentifierFactory;
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory(): AssertionExaminedValueFactory
    {
        return new AssertionExaminedValueFactory(
            DomIdentifierFactory::createFactory(),
            ValueFactory::createFactory()
        );
    }

    /**
     * @param string $identifierString
     *
     * @return ValueInterface
     */
    public function create(string $identifierString): ValueInterface
    {
        $identifierType = IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);

        if (in_array($identifierType, [IdentifierTypes::ELEMENT_SELECTOR, IdentifierTypes::ATTRIBUTE_SELECTOR])) {
            $domIdentifier = $this->domIdentifierFactory->create($identifierString);

            if ($domIdentifier instanceof DomIdentifierInterface) {
                return new DomIdentifierValue($domIdentifier);
            }
        }

        return $this->valueFactory->createFromValueString($identifierString);
    }
}
