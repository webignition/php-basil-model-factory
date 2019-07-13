<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\IdentifierStringExtractor;

use webignition\BasilModelFactory\IdentifierStringExtractor\VariableParameterIdentifierStringExtractor;
use webignition\BasilModelFactory\Tests\DataProvider\VariableParameterIdentifierStringDataProviderTrait;

class VariableParameterIdentifierStringExtractorTest extends \PHPUnit\Framework\TestCase
{
    use VariableParameterIdentifierStringDataProviderTrait;

    /**
     * @var VariableParameterIdentifierStringExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractor = new VariableParameterIdentifierStringExtractor();
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
        $this->assertTrue($this->extractor->handles('$elements.name'));
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
                'string' => '"quoted"',
            ],
        ];
    }

    /**
     * @dataProvider variableParameterIdentifierStringDataProvider
     */
    public function testExtractFromStartReturnsString(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->extractor->extractFromStart($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }
}
