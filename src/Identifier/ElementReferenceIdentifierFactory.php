<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\AttributeReference;
use webignition\BasilModel\Value\ElementReference;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;
use webignition\BasilModelFactory\ValueTypes;

class ElementReferenceIdentifierFactory implements IdentifierTypeFactoryInterface
{
    public static function createFactory()
    {
        return new ElementReferenceIdentifierFactory();
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::ELEMENT_REFERENCE === IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);
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

        if (0 === substr_count($elementReferenceProperty, '.')) {
            return ReferenceIdentifier::createElementReferenceIdentifier(
                new ElementReference($identifierString, $elementReferenceProperty)
            );
        }

        return ReferenceIdentifier::createAttributeReferenceIdentifier(
            new AttributeReference($identifierString, $elementReferenceProperty)
        );
    }
}
