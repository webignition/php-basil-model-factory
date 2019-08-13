<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\PageElementReference\PageElementReference;
use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
use webignition\BasilModelFactory\ValueFactory;

class PageElementReferenceIdentifierFactory implements IdentifierTypeFactoryInterface
{
    private $valueFactory;

    public function __construct(ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;
    }

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

        $identifier = new Identifier(
            IdentifierTypes::PAGE_ELEMENT_REFERENCE,
            $this->valueFactory->createFromValueString($identifierString)
        );


        if (null !== $name) {
            $identifier = $identifier->withName($name);
        }

        return $identifier;
    }
}
