<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Identifier\IdentifierTypes;
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
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeIdentifierDataProvider
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
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeIdentifierDataProvider
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
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeIdentifierDataProvider
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
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeIdentifierDataProvider
     */
    public function testIsNotElementParameterReference(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementParameterReference($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     */
    public function testFindTypeElementSelector(string $identifierString)
    {
        $this->assertSame(IdentifierTypes::ELEMENT_SELECTOR, IdentifierTypeFinder::findType($identifierString));
    }

    /**
     * @dataProvider elementParameterReferenceDataProvider
     */
    public function testFindTypeElementParameterReference(string $identifierString)
    {
        $this->assertSame(IdentifierTypes::ELEMENT_PARAMETER, IdentifierTypeFinder::findType($identifierString));
    }

    /**
     * @dataProvider pageElementReferenceDataProvider
     */
    public function testFindTypePageElementReference(string $identifierString)
    {
        $this->assertSame(IdentifierTypes::PAGE_ELEMENT_REFERENCE, IdentifierTypeFinder::findType($identifierString));
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

    public function pageElementReferenceDataProvider(): array
    {
        return [
            [
                'identifierString' => 'page_import_name.elements.element_name',
            ],
        ];
    }

    public function attributeIdentifierDataProvider(): array
    {
        return [
            [
                'identifierString' =>  '".selector".attribute_name',
            ],
            [
                'identifierString' =>  '".selector .foo".attribute_name',
            ],
            [
                'identifierString' =>  '".selector.foo".attribute_name',
            ],
            [
                'identifierString' =>  '"#id".attribute_name',
            ],
            [
                'identifierString' =>  '".selector[data-foo=bar]".attribute_name',
            ],
            [
                'identifierString' =>  '"/body".attribute_name',
            ],
            [
                'identifierString' =>  '"//foo".attribute_name',
            ],
            [
                'identifierString' =>  '"//*[@id="id"]".attribute_name',
            ],
            [
                'identifierString' =>  '"//hr[@class=\'edge\']".attribute_name',
            ],
        ];
    }
}
