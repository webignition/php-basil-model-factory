<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;
use webignition\BasilModelFactory\ValueTypes;

class DomReferenceIdentifierFactory implements IdentifierTypeFactoryInterface
{
    public static function createFactory()
    {
        return new DomReferenceIdentifierFactory();
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::ELEMENT_REFERENCE ===
            IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);
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

        $domIdentifierReferenceType = 0 === substr_count($elementReferenceProperty, '.')
            ? DomIdentifierReferenceType::ELEMENT
            : DomIdentifierReferenceType::ATTRIBUTE;

        return ReferenceIdentifier::createElementReferenceIdentifier(
            new DomIdentifierReference(
                $domIdentifierReferenceType,
                $identifierString,
                $elementReferenceProperty
            )
        );
    }
}
