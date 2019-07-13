<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use Nyholm\Psr7\Uri;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Page\Page;
use webignition\BasilModel\Page\PageInterface;
use webignition\BasilModel\Value\Value;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilDataStructure\Page as PageData;
use webignition\BasilModelFactory\PageFactory;

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
        $parentIdentifier = new Identifier(
            IdentifierTypes::CSS_SELECTOR,
            new Value(
                ValueTypes::STRING,
                '.form'
            ),
            null,
            'form'
        );

        return [
            'empty page data' => [
                'pageData' => new PageData([]),
                'expectedPage' => new Page(new Uri(''), []),
            ],
            'has url, empty elements data' => [
                'pageData' => new PageData([
                    PageData::KEY_URL => 'http://example.com/',
                ]),
                'expectedPage' => new Page(new Uri('http://example.com/'), []),
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
                    [
                        'css-selector' => new Identifier(
                            IdentifierTypes::CSS_SELECTOR,
                            new Value(
                                ValueTypes::STRING,
                                '.selector'
                            ),
                            null,
                            'css-selector'
                        ),
                    ]
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
                    [
                        'form' => $parentIdentifier,
                        'form_field' =>
                            (new Identifier(
                                IdentifierTypes::CSS_SELECTOR,
                                new Value(
                                    ValueTypes::STRING,
                                    '.field'
                                ),
                                null,
                                'form_field'
                            ))->withParentIdentifier($parentIdentifier),
                    ]
                ),
            ],
        ];
    }
}