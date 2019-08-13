<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\AttributeIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModelFactory\IdentifierTypeFinder;

class AttributeIdentifierFactory implements IdentifierTypeFactoryInterface
{
    private $elementIdentifierFactory;

    public function __construct(ElementIdentifierFactory $elementIdentifierFactory)
    {
        $this->elementIdentifierFactory = $elementIdentifierFactory;
    }

    public static function createFactory(): AttributeIdentifierFactory
    {
        return new AttributeIdentifierFactory(
            ElementIdentifierFactory::createFactory()
        );
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::ATTRIBUTE === IdentifierTypeFinder::findType($identifierString);
    }

    public function create(string $identifierString, ?string $name = null): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);

        list($elementIdentifierString, $attributeName) = $this->extractAttributeNameAndElementIdentifier(
            $identifierString
        );

        $identifier = null;
        $elementIdentifier = $this->elementIdentifierFactory->create($elementIdentifierString);

        if ($elementIdentifier instanceof ElementIdentifierInterface) {
            $identifier = new AttributeIdentifier($elementIdentifier, $attributeName);

            if (null !== $name) {
                $identifier = $identifier->withName($name);
            }
        }

        return $identifier;
    }

    private function extractAttributeNameAndElementIdentifier(string $identifier)
    {
        $lastDotPosition = (int) mb_strrpos($identifier, '.');

        $elementIdentifier = mb_substr($identifier, 0, $lastDotPosition);
        $attributeName = mb_substr($identifier, $lastDotPosition + 1);

        return [
            $elementIdentifier,
            $attributeName
        ];
    }
}
