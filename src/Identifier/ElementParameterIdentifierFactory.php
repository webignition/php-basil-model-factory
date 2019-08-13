<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\ValueFactory;

class ElementParameterIdentifierFactory extends AbstractValueBasedIdentifierFactory implements
    IdentifierTypeFactoryInterface
{
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

        return $this->createForType($identifierString, IdentifierTypes::ELEMENT_PARAMETER, $name);
    }
}
