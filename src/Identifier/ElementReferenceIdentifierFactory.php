<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\ElementReference;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\ValueTypes;

class ElementParameterIdentifierFactory implements IdentifierTypeFactoryInterface
{
    public static function createFactory()
    {
        return new ElementParameterIdentifierFactory();
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypeFinder::TYPE_ELEMENT_REFERENCE === IdentifierTypeFinder::findType($identifierString);
    }

    /**
     * @param string $identifierString
     *
     * @return IdentifierInterface|null
     */
    public function create(string $identifierString): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);

        $elementReferenceProperty = (string) preg_replace(
            '/^\$' . ValueTypes::TYPE_ELEMENT_PARAMETER . '\./',
            '',
            $identifierString
        );

        $identifierType = 0 === substr_count($elementReferenceProperty, '.')
            ? IdentifierTypes::ELEMENT_PARAMETER
            : IdentifierTypes::ATTRIBUTE;

        return new ReferenceIdentifier(
            $identifierType,
            new ElementReference($identifierString, $elementReferenceProperty)
        );
    }
}
