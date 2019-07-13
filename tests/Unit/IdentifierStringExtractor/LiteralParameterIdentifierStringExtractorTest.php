<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\IdentifierStringExtractor;

use webignition\BasilModelFactory\IdentifierStringExtractor\LiteralParameterIdentifierStringExtractor;
use webignition\BasilModelFactory\Tests\Unit\DataProvider\LiteralParameterStringDataProviderTrait;

class LiteralParameterIdentifierStringExtractorTest extends \PHPUnit\Framework\TestCase
{
    use LiteralParameterStringDataProviderTrait;

    /**
     * @var LiteralParameterIdentifierStringExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new LiteralParameterIdentifierStringExtractor();
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
        $this->assertTrue($this->extractor->handles('reference'));
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
            'quoted value' => [
                'string' => '"not handled"',
            ],
            'variable value' => [
                'string' => '$elements.element_name',
            ],
        ];
    }

    /**
     * @dataProvider literalParameterStringDataProvider
     */
    public function testExtractFromStartReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extractFromStart($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
