<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\Identifier\IdentifierFactory;
use webignition\BasilModelFactory\Tests\DataProvider\AttributeIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\CssSelectorIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\DomReferenceIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\PageElementReferenceIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\XpathExpressionIdentifierDataProviderTrait;
use webignition\BasilTestIdentifierFactory\TestIdentifierFactory;

class IdentifierFactoryTest extends \PHPUnit\Framework\TestCase
{
    use CssSelectorIdentifierDataProviderTrait;
    use XpathExpressionIdentifierDataProviderTrait;
    use DomReferenceIdentifierDataProviderTrait;
    use PageElementReferenceIdentifierDataProviderTrait;
    use AttributeIdentifierDataProviderTrait;

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
     * @dataProvider cssSelectorIdentifierDataProvider
     * @dataProvider xpathExpressionIdentifierDataProvider
     * @dataProvider domReferenceIdentifierDataProvider
     * @dataProvider pageElementReferenceIdentifierDataProvider
     * @dataProvider attributeIdentifierDataProvider
     */
    public function testCreateSuccess(string $identifierString, IdentifierInterface $expectedIdentifier)
    {
        $identifier = $this->factory->create($identifierString);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);
        $this->assertEquals($expectedIdentifier, $identifier);
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
                    '.selector',
                    null,
                    'element_name'
                ),
            ],
            'element reference with css selector, position null' => [
                'identifierString' => '"{{ parent_element_name }} .selector"',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    '.selector',
                    null,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with css selector, position 1' => [
                'identifierString' => '"{{ parent_element_name }} .selector":1',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
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
                    '{{ another_element_name }} .selector',
                    null,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with xpath expression, position null' => [
                'identifierString' => '"{{ parent_element_name }} //foo"',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
                    '//foo',
                    null,
                    'element_name',
                    $parentIdentifier
                ),
            ],
            'element reference with xpath expression, position 1' => [
                'identifierString' => '"{{ parent_element_name }} //foo":1',
                'element_name' => 'element_name',
                'existingIdentifiers' => $existingIdentifiers,
                'expectedIdentifier' => TestIdentifierFactory::createElementIdentifier(
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
                    '//foo',
                    2,
                    'element_name',
                    $parentIdentifier
                ),
            ],
        ];
    }

    public function testCreateWithElementReferenceEmpty()
    {
        $this->assertNull($this->factory->createWithElementReference('', 'element_name', []));
        $this->assertNull($this->factory->createWithElementReference(' ', 'element_name', []));
    }

    /**
     * @dataProvider createForUnknownTypeDataProvider
     */
    public function testCreateForUnknownType(string $identifierString)
    {
        $this->assertNull($this->factory->create($identifierString));
    }

    public function createForUnknownTypeDataProvider(): array
    {
        return [
            'empty' => [
                'identifierString' => '',
            ],
            'whitespace only' => [
                'identifierString' => ' ',
            ],
            'unknown type' => [
                'identifierString' => 'invalid-page-model-element-reference',
            ],
        ];
    }
}
