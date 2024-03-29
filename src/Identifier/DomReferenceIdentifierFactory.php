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

        $identifierType = IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);

        return in_array($identifierType, [
            IdentifierTypes::ATTRIBUTE_REFERENCE,
            IdentifierTypes::ELEMENT_REFERENCE,
        ]);
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

        $domIdentifierReference = new DomIdentifierReference(
            $domIdentifierReferenceType,
            $identifierString,
            $elementReferenceProperty
        );

        if (DomIdentifierReferenceType::ATTRIBUTE === $domIdentifierReferenceType) {
            return ReferenceIdentifier::createAttributeReferenceIdentifier($domIdentifierReference);
        }

        return ReferenceIdentifier::createElementReferenceIdentifier($domIdentifierReference);
    }
}
