<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Identifier;

use webignition\BasilModel\Identifier\AttributeIdentifier;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;
use webignition\BasilModelFactory\Tests\Services\TestIdentifierFactory;

class IdentifierFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var IdentifierFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = IdentifierFactory::createFactory();
    }

    /**
     * @dataProvider createCssSelectorDataProvider
     * @dataProvider createXpathExpressionDataProvider
     * @dataProvider createElementParameterDataProvider
     * @dataProvider createPageElementReferenceDataProvider
     * @dataProvider createAttributeIdentifierDataProvider
     */
    public function testCreateSuccess(string $identifierString, IdentifierInterface $expectedIdentifier)
    {
        $identifier = $this->factory->create($identifierString);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);
        $this->assertEquals($expectedIdentifier, $identifier);
    }

    public function createCssSelectorDataProvider(): array
    {
        return [
            'css id selector' => [
                'identifierString' => '"#element-id"',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('#element-id'),
                    1
                ),
            ],
            'css class selector, position: null' => [
                'identifierString' => '".listed-item"',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    1
                ),
            ],
            'css class selector; position: 1' => [
                'identifierString' => '".listed-item":1',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    1
                ),
            ],
            'css class selector; position: 3' => [
                'identifierString' => '".listed-item":3',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    3
                ),
            ],
            'css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    -1
                ),
            ],
            'css class selector; position: -3' => [
                'identifierString' => '".listed-item":-3',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    -3
                ),
            ],
            'css class selector; position: first' => [
                'identifierString' => '".listed-item":first',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    1
                ),
            ],
            'css class selector; position: last' => [
                'identifierString' => '".listed-item":last',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createCssSelectorValue('.listed-item'),
                    -1
                ),
            ],
        ];
    }

    public function createXpathExpressionDataProvider(): array
    {
        return [
            'xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]"',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//*[@id="element-id"]'),
                    1
                ),
            ],
            'xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]"',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    1
                ),
            ],
            'xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    1
                ),
            ],
            'xpath attribute selector; position: 3' => [
                'identifierString' => '"//input[@type="submit"]":3',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    3
                ),
            ],
            'xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    -1
                ),
            ],
            'xpath attribute selector; position: -3' => [
                'identifierString' => '"//input[@type="submit"]":-3',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    -3
                ),
            ],
            'xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    1
                ),
            ],
            'xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last',
                'expectedIdentifier' => new ElementIdentifier(
                    LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                    -1
                ),
            ],
        ];
    }

    public function createElementParameterDataProvider(): array
    {
        return [
            'element parameter' => [
                'identifierString' => '$elements.name',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::ELEMENT_PARAMETER,
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        'elements',
                        'name'
                    )
                ),
            ],
        ];
    }

    public function createPageElementReferenceDataProvider(): array
    {
        return [
            'page model element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                    new ObjectValue(
                        ValueTypes::PAGE_ELEMENT_REFERENCE,
                        'page_import_name.elements.element_name',
                        'page_import_name',
                        'element_name'
                    )
                ),
            ],
        ];
    }

    public function createAttributeIdentifierDataProvider(): array
    {
        return [
            'attribute identifier: css class selector, position: null' => [
                'identifierString' => '".listed-item".attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.listed-item'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: 1' => [
                'identifierString' => '".listed-item":1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.listed-item'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.listed-item'),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: first' => [
                'identifierString' => '".listed-item":first.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.listed-item'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: css class selector; position: last' => [
                'identifierString' => '".listed-item":last.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.listed-item'),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath id selector' => [
                'identifierString' => '"//*[@id="element-id"]".attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//*[@id="element-id"]'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]".attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                        1
                    ),
                    'attribute_name'
                ),
            ],
            'attribute identifier: xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last.attribute_name',
                'expectedIdentifier' => new AttributeIdentifier(
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//input[@type="submit"]'),
                        -1
                    ),
                    'attribute_name'
                ),
            ],
        ];
    }

    /**
     * @dataProvider createReferencedElementDataProvider
     */
    public function testCreateWithElementReference(
        string $identifierString,
        string $elementName,
        array $existingIdentifiers,
        IdentifierInterface $expectedIdentifier
    ) {
        $identifier = $this->factory->createWithElementReference($identifierString, $elementName, $existingIdentifiers);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);

        if ($identifier instanceof IdentifierInterface) {
            $this->assertEquals($expectedIdentifier, $identifier);
        }
    }

    public function createReferencedElementDataProvider(): array
    {
        $parentIdentifier = TestIdentifierFactory::createElementIdentifier(
            ValueTypes::CSS_SELECTOR,
            '.parent',
            1,
            'parent_element_name',
            null
        );

        $existingIdentifiers = [
            'parent_element_name' => $parentIdentifier,
        ];

        return [
            'element reference with css selector, position null, parent identifier not passed' => [
                'identifierString' => '"{{ parent_element_name }} .selector"',
                'element_name' => 'element_name',
                'existingIdentifiers' => [],
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::CSS_SELECTOR,
                    '.selector',
                    1,
                    'element_name'
                ),
            ],
            'element reference with css selector, position null' => [
                'identifierString' => '"{{ parent_element_name }} .selector"',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::CSS_SELECTOR,
                    '.selector',
                    1,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with css selector, position 1' => [
                'identifierString' => '"{{ parent_element_name }} .selector":1',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::CSS_SELECTOR,
                    '.selector',
                    1,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with css selector, position 2' => [
                'identifierString' => '"{{ parent_element_name }} .selector":2',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::CSS_SELECTOR,
                    '.selector',
                    2,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'invalid double element reference with css selector' => [
                'identifierString' => '"{{ parent_element_name }} {{ another_element_name }} .selector"',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::CSS_SELECTOR,
                    '{{ another_element_name }} .selector',
                    1,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with xpath expression, position null' => [
                'identifierString' => '"{{ parent_element_name }} //foo"',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::XPATH_EXPRESSION,
                    '//foo',
                    1,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with xpath expression, position 1' => [
                'identifierString' => '"{{ parent_element_name }} //foo":1',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::XPATH_EXPRESSION,
                    '//foo',
                    1,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with xpath expression, position 2' => [
                'identifierString' => '"{{ parent_element_name }} //foo":2',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    ValueTypes::XPATH_EXPRESSION,
                    '//foo',
                    2,
                    'element_name',
                    $parentIdentifier
                ),
            ],
        ];
    }

    public function testCreateEmpty()
    {
        $this->assertNull($this->factory->create(''));
        $this->assertNull($this->factory->create(' '));
    }

    public function testCreateWithElementReferenceEmpty()
    {
        $this->assertNull($this->factory->createWithElementReference('', 'element_name', []));
        $this->assertNull($this->factory->createWithElementReference(' ', 'element_name', []));
    }

    public function testCreateForMalformedPageElementReference()
    {
        $this->expectException(MalformedPageElementReferenceException::class);
        $this->expectExceptionMessage('Malformed page element reference "invalid-page-model-element-reference"');

        $this->factory->create('invalid-page-model-element-reference');
    }
}