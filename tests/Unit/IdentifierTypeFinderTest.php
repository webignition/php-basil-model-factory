<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModelFactory\IdentifierTypeFinder;

class IdentifierTypeFinderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider cssSelectorDataProvider
     */
    public function testIsCssSelector(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isCssSelector($identifierString));
    }

    /**
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider elementParameterReferenceDataProvider
     */
    public function testIsNotCssSelector(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isCssSelector($identifierString));
    }

    /**
     * @dataProvider xPathExpressionDataProvider
     */
    public function testIsXpathExpression(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isXpathExpression($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider elementParameterReferenceDataProvider
     */
    public function testIsNotXpathExpression(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isXpathExpression($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     */
    public function testIsElementIdentifier(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isElementIdentifier($identifierString));
    }

    /**
     * @dataProvider elementParameterReferenceDataProvider
     */
    public function testIsNotElementIdentifier(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementIdentifier($identifierString));
    }

    /**
     * @dataProvider elementParameterReferenceDataProvider
     */
    public function testIsElementParameterReference(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isElementParameterReference($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     */
    public function testIsNotElementParameterReference(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementParameterReference($identifierString));
    }

    public function cssSelectorDataProvider(): array
    {
        return [
            [
                'identifierString' =>  '".selector"',
            ],
            [
                'identifierString' =>  '".selector .foo"',
            ],
            [
                'identifierString' =>  '".selector.foo"',
            ],
            [
                'identifierString' =>  '"#id"',
            ],
            [
                'identifierString' =>  '".selector[data-foo=bar]"',
            ],
        ];
    }


    public function xPathExpressionDataProvider(): array
    {
        return [
            [
                'identifierString' =>  '"/body"',
            ],
            [
                'identifierString' =>  '"//foo"',
            ],
            [
                'identifierString' =>  '"//*[@id="id"]"',
            ],
            [
                'identifierString' =>  '"//hr[@class=\'edge\']"',
            ],
        ];
    }

    public function elementParameterReferenceDataProvider(): array
    {
        return [
            [
                'identifierString' =>  '$elements.element_name',
            ],
        ];
    }
}
