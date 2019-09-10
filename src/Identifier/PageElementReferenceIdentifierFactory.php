<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\PageElementReference\PageElementReference as PageElementReferenceModel;
use webignition\BasilModel\Value\PageElementReference as PageElementReferenceValue;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;

class PageElementReferenceIdentifierFactory implements IdentifierTypeFactoryInterface
{
    public static function createFactory()
    {
        return new PageElementReferenceIdentifierFactory();
    }

    public function handles(string $identifierString): bool
    {
        if ('' === trim($identifierString)) {
            return false;
        }

        return IdentifierTypes::PAGE_ELEMENT_REFERENCE === IdentifierTypeFinder::findTypeFromIdentifierString($identifierString);
    }

    /**
     * @param string $identifierString
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function create(string $identifierString): ?IdentifierInterface
    {
        if (!$this->handles($identifierString)) {
            return null;
        }

        $identifierString = trim($identifierString);
        $pageElementReference = new PageElementReferenceModel($identifierString);

        if (!$pageElementReference->isValid()) {
            throw new MalformedPageElementReferenceException($pageElementReference);
        }

        return ReferenceIdentifier::createPageElementReferenceIdentifier(
            new PageElementReferenceValue(
                $identifierString,
                $pageElementReference->getImportName(),
                $pageElementReference->getElementName()
            )
        );
    }
}
