<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\ValueFactory;

class ElementParameterIdentifierFactory implements IdentifierTypeFactoryInterface
{
    private $valueFactory;

    public function __construct(ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory()
    {
        return new ElementParameterIdentifierFactory(
            ValueFactory::createFactory()
        );
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::ELEMENT_PARAMETER === IdentifierTypeFinder::findType($identifierString);
    }

    /**
     * @param string $identifierString
     * @param string|null $name
     *
     * @return IdentifierInterface|null
     */
    public function create(string $identifierString, ?string $name = null): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);

        $identifier = new Identifier(
            IdentifierTypes::ELEMENT_PARAMETER,
            $this->valueFactory->createFromValueString($identifierString)
        );

        if (null !== $name) {
            $identifier = $identifier->withName($name);
        }

        return $identifier;
    }
}
