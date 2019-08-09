<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\IdentifierStringExtractor;

use webignition\BasilModelFactory\IdentifierStringExtractor\IdentifierStringExtractor;
use webignition\BasilModelFactory\Tests\DataProvider\LiteralParameterStringDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\PageElementIdentifierStringDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\VariableParameterIdentifierStringDataProviderTrait;

class IdentifierStringExtractorTest extends \PHPUnit\Framework\TestCase
{
    use LiteralParameterStringDataProviderTrait;
    use PageElementIdentifierStringDataProviderTrait;
    use VariableParameterIdentifierStringDataProviderTrait;

    /**
     * @var IdentifierStringExtractor
     */
    private $identifierStringExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->identifierStringExtractor = IdentifierStringExtractor::create();
    }

    /**
     * @dataProvider extractFromStartDataProvider
     * @dataProvider literalParameterStringDataProvider
     * @dataProvider pageElementIdentifierStringDataProvider
     * @dataProvider variableParameterIdentifierStringDataProvider
     */
    public function testExtractFromStart(string $string, string $expectedIdentifierString)
    {
        $identifierString = $this->identifierStringExtractor->extractFromStart($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }

    public function extractFromStartDataProvider(): array
    {
        return [
            'empty' => [
                'string' => '',
                'expectedIdentifierString' => '',
            ],
        ];
    }
}
