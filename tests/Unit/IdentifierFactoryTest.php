<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\Value;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilModelFactory\IdentifierFactory;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;

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
     * @dataProvider createPageModelElementReferenceDataProvider
     * @dataProvider createPageObjectParameterDataProvider
     * @dataProvider createBrowserObjectParameterDataProvider
     */
    public function testCreateSuccess(
        string $identifierString,
        IdentifierInterface $expectedIdentifier
    ) {
        $identifier = $this->factory->create($identifierString);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);
        $this->assertEquals($expectedIdentifier, $identifier);
    }

    public function createCssSelectorDataProvider(): array
    {
        return [
            'css id selector' => [
                'identifierString' => '"#element-id"',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '#element-id'
                    ),
                    1
                ),
            ],
            'css class selector, position: null' => [
                'identifierString' => '".listed-item"',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
                    1
                ),
            ],
            'css class selector; position: 1' => [
                'identifierString' => '".listed-item":1',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
                    1
                ),
            ],
            'css class selector; position: 3' => [
                'identifierString' => '".listed-item":3',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
                    3
                ),
            ],
            'css class selector; position: -1' => [
                'identifierString' => '".listed-item":-1',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
                    -1
                ),
            ],
            'css class selector; position: -3' => [
                'identifierString' => '".listed-item":-3',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
                    -3
                ),
            ],
            'css class selector; position: first' => [
                'identifierString' => '".listed-item":first',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
                    1
                ),
            ],
            'css class selector; position: last' => [
                'identifierString' => '".listed-item":last',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.listed-item'
                    ),
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
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//*[@id="element-id"]'
                    ),
                    1
                ),
            ],
            'xpath attribute selector, position: null' => [
                'identifierString' => '"//input[@type="submit"]"',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
                    1
                ),
            ],
            'xpath attribute selector; position: 1' => [
                'identifierString' => '"//input[@type="submit"]":1',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
                    1
                ),
            ],
            'xpath attribute selector; position: 3' => [
                'identifierString' => '"//input[@type="submit"]":3',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
                    3
                ),
            ],
            'xpath attribute selector; position: -1' => [
                'identifierString' => '"//input[@type="submit"]":-1',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
                    -1
                ),
            ],
            'xpath attribute selector; position: -3' => [
                'identifierString' => '"//input[@type="submit"]":-3',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
                    -3
                ),
            ],
            'xpath attribute selector; position: first' => [
                'identifierString' => '"//input[@type="submit"]":first',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
                    1
                ),
            ],
            'xpath attribute selector; position: last' => [
                'identifierString' => '"//input[@type="submit"]":last',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::XPATH_EXPRESSION,
                    new Value(
                        ValueTypes::STRING,
                        '//input[@type="submit"]'
                    ),
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

    public function createPageModelElementReferenceDataProvider(): array
    {
        return [
            'page model element reference' => [
                'identifierString' => 'page_import_name.elements.element_name',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                    new Value(
                        ValueTypes::PAGE_MODEL_REFERENCE,
                        'page_import_name.elements.element_name'
                    )
                ),
            ],
        ];
    }

    public function createPageObjectParameterDataProvider(): array
    {
        return [
            'page object parameter' => [
                'identifierString' => '$page.title',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::PAGE_OBJECT_PARAMETER,
                    new ObjectValue(
                        ValueTypes::PAGE_OBJECT_PROPERTY,
                        '$page.title',
                        'page',
                        'title'
                    )
                ),
            ],
        ];
    }

    public function createBrowserObjectParameterDataProvider(): array
    {
        return [
            'browser object parameter' => [
                'identifierString' => '$browser.size',
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::BROWSER_OBJECT_PARAMETER,
                    new ObjectValue(
                        ValueTypes::BROWSER_OBJECT_PROPERTY,
                        '$browser.size',
                        'browser',
                        'size'
                    )
                ),
            ],
        ];
    }

    /**
     * @dataProvider createReferencedElementDataProvider
     */
    public function testCreateWithElementReference(
        string $identifierString,
        array $existingIdentifiers,
        IdentifierInterface $expectedIdentifier
    ) {
        $identifier = $this->factory->createWithElementReference($identifierString, null, $existingIdentifiers);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);

        if ($identifier instanceof IdentifierInterface) {
            $this->assertEquals($expectedIdentifier, $identifier);
        }
    }

    public function createReferencedElementDataProvider(): array
    {
        $parentIdentifier = new Identifier(
            IdentifierTypes::CSS_SELECTOR,
            new Value(
                ValueTypes::STRING,
                '.parent'
            ),
            null,
            'element_name'
        );

        $existingIdentifiers = [
            'element_name' => $parentIdentifier,
        ];

        return [
            'element reference with css selector, position null, parent identifier not passed' => [
                'identifierString' => '"{{ element_name }} .selector"',
                'existingIdentifiers' => [],
                'expectedIdentifier' => new Identifier(
                    IdentifierTypes::CSS_SELECTOR,
                    new Value(
                        ValueTypes::STRING,
                        '.selector'
                    )
                ),
            ],
            'element reference with css selector, position null' => [
                'identifierString' => '"{{ element_name }} .selector"',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '.selector'
                        )
                    ))->withParentIdentifier($parentIdentifier),
            ],
            'element reference with css selector, position 1' => [
                'identifierString' => '"{{ element_name }} .selector":1',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '.selector'
                        )
                    ))->withParentIdentifier($parentIdentifier),
            ],
            'element reference with css selector, position 2' => [
                'identifierString' => '"{{ element_name }} .selector":2',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '.selector'
                        ),
                        2
                    ))->withParentIdentifier($parentIdentifier),
            ],
            'invalid double element reference with css selector' => [
                'identifierString' => '"{{ element_name }} {{ another_element_name }} .selector"',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '{{ another_element_name }} .selector'
                        )
                    ))->withParentIdentifier($parentIdentifier),
            ],
            'element reference with xpath expression, position null' => [
                'identifierString' => '"{{ element_name }} //foo"',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//foo'
                        )
                    ))->withParentIdentifier($parentIdentifier),
            ],
            'element reference with xpath expression, position 1' => [
                'identifierString' => '"{{ element_name }} //foo":1',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//foo'
                        )
                    ))->withParentIdentifier($parentIdentifier),
            ],
            'element reference with xpath expression, position 2' => [
                'identifierString' => '"{{ element_name }} //foo":2',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' =>
                    (new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//foo'
                        ),
                        2
                    ))->withParentIdentifier($parentIdentifier),
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
        $this->assertNull($this->factory->createWithElementReference('', null, []));
        $this->assertNull($this->factory->createWithElementReference(' ', null, []));
    }

    public function testCreateForMalformedPageElementReference()
    {
        $this->expectException(MalformedPageElementReferenceException::class);
        $this->expectExceptionMessage('Malformed page element reference "invalid-page-model-element-reference"');

        $this->factory->create('invalid-page-model-element-reference');
    }
}
