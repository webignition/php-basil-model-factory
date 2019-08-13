<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
use webignition\BasilModelFactory\ValueFactory;

class PageElementReferenceIdentifierFactory extends AbstractValueBasedIdentifierFactory implements
    IdentifierTypeFactoryInterface
{
    public static function createFactory()
    {
        return new PageElementReferenceIdentifierFactory(
            ValueFactory::createFactory()
        );
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::PAGE_ELEMENT_REFERENCE === IdentifierTypeFinder::findType($identifierString);
    }

    /**
     * @param string $identifierString
     * @param string|null $name
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function create(string $identifierString, ?string $name = null): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);
        $pageElementReference = new PageElementReference($identifierString);

        if (!$pageElementReference->isValid()) {
            throw new MalformedPageElementReferenceException($pageElementReference);
        }

        return $this->createForType($identifierString, IdentifierTypes::PAGE_ELEMENT_REFERENCE, $name);
    }
}
