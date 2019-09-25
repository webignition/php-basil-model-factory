<?php

namespace webignition\BasilModelFactory;

use Nyholm\Psr7\Uri;
use webignition\BasilModel\Identifier\DomIdentifierCollection;
use webignition\BasilModel\Identifier\DomIdentifierInterface;
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

        return new Page($uri, $elementIdentifiers);
    }

    /**
     * @param array $elementData
     *
     * @return DomIdentifierCollection
     *
     * @throws InvalidPageElementIdentifierException
     */
    private function createElementIdentifiers(array $elementData): DomIdentifierCollection
    {
        /** @var DomIdentifierInterface[] $elementIdentifiers */
        $elementIdentifiers = [];

        foreach ($elementData as $elementName => $identifierString) {
            $identifier = $this->identifierFactory->createWithElementReference(
                $identifierString,
                $elementName,
                $elementIdentifiers
            );

            if (null !== $identifier) {
                if (!$identifier instanceof DomIdentifierInterface ||
                    ($identifier instanceof DomIdentifierInterface && null !== $identifier->getAttributeName())) {
                    throw new InvalidPageElementIdentifierException($identifier);
                }
            }

            if ($identifier instanceof DomIdentifierInterface) {
                $elementIdentifiers[$elementName] = $identifier;
            }
        }

        return new DomIdentifierCollection($elementIdentifiers);
    }
}
