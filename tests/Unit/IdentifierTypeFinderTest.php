<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;
use webignition\BasilModel\Value\PageElementReference;
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
     * @dataProvider attributeSelectorDataProvider
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
     * @dataProvider attributeSelectorDataProvider
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
     * @dataProvider attributeSelectorDataProvider
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsNotElementIdentifier(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementIdentifier($identifierString));
    }

    /**
     * @dataProvider elementReferenceDataProvider
     */
    public function testIsElementReference(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isElementReference($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeSelectorDataProvider
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsNotElementReference(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isElementReference($identifierString));
    }

    /**
     * @dataProvider attributeSelectorDataProvider
     */
    public function testIsAttributeIdentifier(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isAttributeIdentifier($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider elementReferenceDataProvider
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsNotAttributeIdentifier(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isAttributeIdentifier($identifierString));
    }

    /**
     * @dataProvider attributeReferenceDataProvider
     */
    public function testIsAttributeReference(string $identifierString)
    {
        $this->assertTrue(IdentifierTypeFinder::isAttributeReference($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xPathExpressionDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider elementReferenceDataProvider
     * @dataProvider attributeSelectorDataProvider
     */
    public function testIsNotAttributeReference(string $identifierString)
    {
        $this->assertFalse(IdentifierTypeFinder::isAttributeReference($identifierString));
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
     * @dataProvider attributeReferenceDataProvider
     */
    public function testFindTypeAttributeReference(string $identifierString)
    {
        $this->assertSame(
            IdentifierTypes::ATTRIBUTE_REFERENCE,
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
     * @dataProvider attributeSelectorDataProvider
     */
    public function testFindTypeAttributeSelector(string $identifierString)
    {
        $this->assertSame(
            IdentifierTypes::ATTRIBUTE_SELECTOR,
            IdentifierTypeFinder::findTypeFromIdentifierString($identifierString)
        );
    }

    /**
     * @dataProvider unknownTypeDataProvider
     */
    public function testFindTypeUnknownType(string $identifierString)
    {
        $this->assertNull(IdentifierTypeFinder::findTypeFromIdentifierString($identifierString));
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

    public function attributeReferenceDataProvider(): array
    {
        return [
            [
                'identifierString' =>  '$elements.element_name.attribute_name',
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

    public function attributeSelectorDataProvider(): array
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

    public function unknownTypeDataProvider(): array
    {
        return  [
            'empty' => [
                'identifierString' => '',
            ],
            'unknown type' => [
                'identifierString' => 'invalid',
            ],
        ];
    }

    /**
     * @dataProvider findTypeFromIdentifierDataProvider
     */
    public function testFindTypeFromIdentifier(IdentifierInterface $identifier, string $expectedType)
    {
        $this->assertSame(IdentifierTypeFinder::findTypeFromIdentifier($identifier), $expectedType);
    }

    public function findTypeFromIdentifierDataProvider(): array
    {
        return [
            'attribute selector' => [
                'identifier' => (new DomIdentifier(
                    new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                ))->withAttributeName('attribute_name'),
                'expectedType' => IdentifierTypes::ATTRIBUTE_SELECTOR,
            ],
            'css element selector' => [
                'identifier' => new DomIdentifier(
                    new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                ),
                'expectedType' => IdentifierTypes::ELEMENT_SELECTOR,
            ],
            'xpath element selector' => [
                'identifier' => new DomIdentifier(
                    new ElementExpression('//h1', ElementExpressionType::XPATH_EXPRESSION)
                ),
                'expectedType' => IdentifierTypes::ELEMENT_SELECTOR,
            ],
            'attribute reference' => [
                'identifier' => ReferenceIdentifier::createAttributeReferenceIdentifier(
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ATTRIBUTE,
                        '$elements.element_name.attribute_name',
                        'element_name.attribute_name'
                    )
                ),
                'expectedType' => IdentifierTypes::ATTRIBUTE_REFERENCE,
            ],
            'element reference' => [
                'identifier' => ReferenceIdentifier::createElementReferenceIdentifier(
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ELEMENT,
                        '$elements.element_name',
                        'element_name'
                    )
                ),
                'expectedType' => IdentifierTypes::ELEMENT_REFERENCE,
            ],
            'page element reference' => [
                'identifier' => ReferenceIdentifier::createPageElementReferenceIdentifier(
                    new PageElementReference(
                        'page_import_name.elements.element_name',
                        'page_import_name',
                        'element_name'
                    )
                ),
                'expectedType' => IdentifierTypes::PAGE_ELEMENT_REFERENCE,
            ],
        ];
    }
}
