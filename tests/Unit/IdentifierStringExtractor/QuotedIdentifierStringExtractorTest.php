<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\IdentifierStringExtractor;

use webignition\BasilModelFactory\IdentifierStringExtractor\QuotedIdentifierStringExtractor;
use webignition\BasilModelFactory\Tests\DataProvider\QuotedIdentifierStringDataProviderTrait;

class QuotedIdentifierStringExtractorTest extends \PHPUnit\Framework\TestCase
{
    use QuotedIdentifierStringDataProviderTrait;

    /**
     * @var QuotedIdentifierStringExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new QuotedIdentifierStringExtractor();
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testHandlesReturnsFalse(string $string)
    {
        $this->assertFalse($this->extractor->handles($string));
    }

    public function testHandlesReturnsTrue()
    {
        $this->assertTrue($this->extractor->handles('"quoted"'));
    }

    /**
     * @dataProvider unhandledStringsDataProvider
     */
    public function testExtractFromStartReturnsNull(string $string)
    {
        $this->assertNull($this->extractor->extractFromStart($string));
    }

    public function unhandledStringsDataProvider(): array
    {
        return [
            'empty' => [
                'string' => '',
            ],
            'variable value' => [
                'string' => '$elements.element_name',
            ],
        ];
    }

    /**
     * @dataProvider quotedIdentifierStringDataProvider
     */
    public function testExtractFromStartReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extractFromStart($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
