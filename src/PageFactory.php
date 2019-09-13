<?php

namespace webignition\BasilModelFactory;

use Nyholm\Psr7\Uri;
use webignition\BasilModel\Identifier\ElementIdentifierCollection;
use webignition\BasilModel\Identifier\ElementIdentifierInterface;
use webignition\BasilModel\Page\Page;
use webignition\BasilModel\Page\PageInterface;
use webignition\BasilDataStructure\Page as PageData;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;

class PageFactory
{
    /**
     * @var IdentifierFactory
     */
    private $identifierFactory;

    public function __construct(IdentifierFactory $identifierFactory)
    {
        $this->identifierFactory = $identifierFactory;
    }

    public static function create(): PageFactory
    {
        return new PageFactory(
            IdentifierFactory::createFactory()
        );
    }

    /**
     * @param PageData $pageData
     *
     * @return PageInterface
     *
     * @throws InvalidPageElementIdentifierException
     */
    public function createFromPageData(PageData $pageData): PageInterface
    {
        $uriString = $pageData->getUrl();
        $elementData = $pageData->getElements();

        $uri = new Uri($uriString);

        $elementIdentifiers = $this->createElementIdentifiers($elementData);
        $elementIdentifiers = $this->resolveNonPositionedParentIdentifiers($elementIdentifiers);

        return new Page($uri, $elementIdentifiers);
    }

    /**
     * @param array $elementData
     *
     * @return ElementIdentifierCollection
     *
     * @throws InvalidPageElementIdentifierException
     */
    private function createElementIdentifiers(array $elementData): ElementIdentifierCollection
    {
        /** @var ElementIdentifierInterface[] $elementIdentifiers */
        $elementIdentifiers = [];

        foreach ($elementData as $elementName => $identifierString) {
            $identifier = $this->identifierFactory->createWithElementReference(
                $identifierString,
                $elementName,
                $elementIdentifiers
            );

            if (null !== $identifier && !$identifier instanceof ElementIdentifierInterface) {
                throw new InvalidPageElementIdentifierException($identifier);
            }

            if ($identifier instanceof ElementIdentifierInterface) {
                $elementIdentifiers[$elementName] = $identifier;
            }
        }

        return new ElementIdentifierCollection($elementIdentifiers);
    }

    /**
     * @param ElementIdentifierCollection $elementIdentifiers
     *
     * @return ElementIdentifierCollection]
     */
    private function resolveNonPositionedParentIdentifiers(
        ElementIdentifierCollection $elementIdentifiers
    ): ElementIdentifierCollection {
        foreach ($elementIdentifiers as $identifier) {
            $isParentIdentifier = $this->isParentIdentifier($identifier, $elementIdentifiers);
            $hasPosition = null !== $identifier->getPosition();

            if ($isParentIdentifier && !$hasPosition) {
                $elementIdentifiers = $elementIdentifiers->replace(
                    $identifier,
                    $identifier->withPosition(1)
                );
            }
        }

        $elementIdentifiers->rewind();

        return $elementIdentifiers;
    }

    private function isParentIdentifier(
        ElementIdentifierInterface $identifier,
        ElementIdentifierCollection $elementIdentifiers
    ): bool {
        foreach ($elementIdentifiers as $elementIdentifier) {
            if ($elementIdentifier instanceof ElementIdentifierInterface) {
                $parentIdentifier = $elementIdentifier->getParentIdentifier();

                if (null !== $parentIdentifier) {
                    return $parentIdentifier === $identifier;
                }
            }
        }

        return false;
    }
}
