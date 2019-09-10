<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModelFactory\IdentifierTypeFinder;
use webignition\BasilModelFactory\IdentifierTypes;

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
     * @dataProvider elementReferenceDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeReferenceDataProvider
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
     * @dataProvider elementReferenceDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeReferenceDataProvider
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
     * @dataProvider elementReferenceDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsNotElementIdentifier(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementIdentifier($identifierString));
    }

    /**
     * @dataProvider elementReferenceDataProvider
     */
    public function testIsElementParameterReference(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isElementReference($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsNotElementParameterReference(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementReference($identifierString));
    }

    /**
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsAttributeIdentifier(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isAttributeReference($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     */
    public function testFindTypeElementSelector(string $identifierString)
    {
        $this->assertSame(
            IdentifierTypes::ELEMENT_SELECTOR,
            IdentifierTypeFinder::findTypeFromIdentifierString($identifierString)
        );
    }

    /**
     * @dataProvider elementReferenceDataProvider
     */
    public function testFindTypeElementReference(string $identifierString)
    {
        $this->assertSame(
            IdentifierTypes::ELEMENT_REFERENCE,
            IdentifierTypeFinder::findTypeFromIdentifierString($identifierString)
        );
    }

    /**
     * @dataProvider pageElementReferenceDataProvider
     */
    public function testFindTypePageElementReference(string $identifierString)
    {
        $this->assertSame(
            IdentifierTypes::PAGE_ELEMENT_REFERENCE,
            IdentifierTypeFinder::findTypeFromIdentifierString($identifierString)
        );
    }

    /**
     * @dataProvider attributeReferenceDataProvider
     */
    public function testFindTypeAttributeReference(string $identifierString)
    {
        $this->assertSame(
            IdentifierTypes::ATTRIBUTE_REFERENCE,
            IdentifierTypeFinder::findTypeFromIdentifierString($identifierString)
        );
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
            [
                'identifierString' =>  '".selector":0',
            ],
            [
                'identifierString' =>  '".selector":1',
            ],
            [
                'identifierString' =>  '".selector":-1',
            ],
            [
                'identifierString' =>  '".selector":first',
            ],
            [
                'identifierString' =>  '".selector":last',
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
            [
                'identifierString' =>  '"/body":0',
            ],
            [
                'identifierString' =>  '"/body":1',
            ],
            [
                'identifierString' =>  '"/body":-1',
            ],
            [
                'identifierString' =>  '"/body":first',
            ],
            [
                'identifierString' =>  '"/body":last',
            ],
        ];
    }

    public function elementReferenceDataProvider(): array
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

    public function attributeReferenceDataProvider(): array
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
            [
                'identifierString' =>  '".selector":0.attribute_name',
            ],
            [
                'identifierString' =>  '".selector":1.attribute_name',
            ],
            [
                'identifierString' =>  '".selector":-1.attribute_name',
            ],
            [
                'identifierString' =>  '".selector":first.attribute_name',
            ],
            [
                'identifierString' =>  '".selector":last.attribute_name',
            ],
        ];
    }
}
