<?php

namespace webignition\BasilModelFactory;

use Nyholm\Psr7\Uri;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Page\Page;
use webignition\BasilModel\Page\PageInterface;
use webignition\BasilParser\DataStructure\Page as PageData;
use webignition\BasilParser\Exception\MalformedPageElementReferenceException;

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

    /**
     * @param PageData $pageData
     *
     * @return PageInterface
     *
     * @throws MalformedPageElementReferenceException
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

            if ($identifier instanceof IdentifierInterface) {
                $elementIdentifiers[$elementName] = $identifier;
            }
        }

        return new Page($uri, $elementIdentifiers);
    }
}
