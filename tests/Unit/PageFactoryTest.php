<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use Nyholm\Psr7\Uri;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\DomIdentifierCollection;
use webignition\BasilModel\Page\Page;
use webignition\BasilModel\Page\PageInterface;
use webignition\BasilDataStructure\Page as PageData;
use webignition\BasilModelFactory\InvalidPageElementIdentifierException;
use webignition\BasilModelFactory\PageFactory;
use webignition\BasilTestIdentifierFactory\TestIdentifierFactory;

class PageFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageFactory = PageFactory::create();
    }

    /**
     * @dataProvider createFromPageDataDataProvider
     */
    public function testCreateFromPageData(PageData $pageData, PageInterface $expectedPage)
    {
        $page = $this->pageFactory->createFromPageData($pageData);

        $this->assertInstanceOf(PageInterface::class, $page);
        $this->assertEquals($expectedPage, $page);
    }

    public function createFromPageDataDataProvider(): array
    {
        $parentIdentifier = TestIdentifierFactory::createElementIdentifier(
            '.form',
            1,
            'form'
        );

        return [
            'empty page data' => [
                'pageData' => new PageData([]),
                'expectedPage' => new Page(new Uri(''), new DomIdentifierCollection()),
            ],
            'has url, empty elements data' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                ]),
                'expectedPage' => new Page(new Uri('http://example.com/'), new DomIdentifierCollection()),
            ],
            'single element identifier' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                    PageData::KEY_ELEMENTS => [
                        'css-selector' => '".selector"',
                    ],
                ]),
                'expectedPage' => new Page(
                    new Uri('http://example.com/'),
                    new DomIdentifierCollection([
                        'css-selector' => (new DomIdentifier('.selector'))->withName('css-selector'),
                    ])
                ),
            ],
            'referenced element identifier' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                    PageData::KEY_ELEMENTS => [
                        'form' => '".form"',
                        'form_field' => '"{{ form }} .field"',
                    ],
                ]),
                'expectedPage' => new Page(
                    new Uri('http://example.com/'),
                    new DomIdentifierCollection([
                        'form' => $parentIdentifier,
                        'form_field' => TestIdentifierFactory::createElementIdentifier(
                            '.field',
                            null,
                            'form_field',
                            $parentIdentifier
                        ),
                    ])
                ),
            ],
        ];
    }

    /**
     * @dataProvider createFromPageDataThrowsInvalidPageElementIdentifierExceptionDataProvider
     */
    public function testCreateFromPageDataThrowsInvalidPageElementIdentifierException(
        PageData $pageData,
        string $expectedExceptionMessage
    ) {
        $this->expectException(InvalidPageElementIdentifierException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->pageFactory->createFromPageData($pageData);
    }

    public function createFromPageDataThrowsInvalidPageElementIdentifierExceptionDataProvider(): array
    {
        return [
            'page element reference' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                    PageData::KEY_ELEMENTS => [
                        'name' => 'page_import_name.elements.element_name',
                    ],
                ]),
                'expectedExceptionMessage' =>
                    'Invalid page element identifier "page_import_name.elements.element_name"',
            ],
            'element parameter' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                    PageData::KEY_ELEMENTS => [
                        'name' => '$elements.element_name',
                    ],
                ]),
                'expectedExceptionMessage' =>
                    'Invalid page element identifier "$elements.element_name"',
            ],
            'attribute parameter' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                    PageData::KEY_ELEMENTS => [
                        'name' => '$elements.element_name.attribute_name',
                    ],
                ]),
                'expectedExceptionMessage' =>
                    'Invalid page element identifier "$elements.element_name.attribute_name"',
            ],
            'attribute selector' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                    PageData::KEY_ELEMENTS => [
                        'name' => '".selector".attribute_name',
                    ],
                ]),
                'expectedExceptionMessage' =>
                    'Invalid page element identifier "".selector".attribute_name"',
            ],
        ];
    }
}
