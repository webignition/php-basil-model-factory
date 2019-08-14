<?php

namespace webignition\BasilModelFactory;

use Nyholm\Psr7\Uri;
use webignition\BasilModel\Identifier\IdentifierCollection;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
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
     * @throws MalformedPageElementReferenceException
     * @throws InvalidPageElementIdentifierException
     */
    public function createFromPageData(PageData $pageData): PageInterface
    {
        $uriString = $pageData->getUrl();
        $elementData = $pageData->getElements();

        $uri = new Uri($uriString);

        $elementIdentifiers = [];

        foreach ($elementData as $elementName => $identifierString) {
            $identifier = $this->identifierFactory->createWithElementReference(
                $identifierString,
                $elementName,
                $elementIdentifiers
            );

            if (null !== $identifier && IdentifierTypes::ELEMENT_SELECTOR !== $identifier->getType()) {
                throw new InvalidPageElementIdentifierException($identifier);
            }

            if ($identifier instanceof IdentifierInterface) {
                $elementIdentifiers[$elementName] = $identifier;
            }
        }

        return new Page($uri, new IdentifierCollection($elementIdentifiers));
    }
}
