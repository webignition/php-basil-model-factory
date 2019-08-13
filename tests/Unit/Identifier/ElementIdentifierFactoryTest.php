<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\Identifier\ElementIdentifierFactory;
use webignition\BasilModelFactory\Tests\DataProvider\AttributeIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\AttributeIdentifierStringDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\CssSelectorDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\CssSelectorIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\ElementParameterDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\ElementParameterIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\PageElementReferenceDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\PageElementReferenceIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\XpathExpressionDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\XpathExpressionIdentifierDataProviderTrait;

class ElementIdentifierFactoryTest extends \PHPUnit\Framework\TestCase
{
    use CssSelectorDataProviderTrait;
    use XpathExpressionDataProviderTrait;
    use ElementParameterDataProviderTrait;
    use PageElementReferenceDataProviderTrait;
    use AttributeIdentifierStringDataProviderTrait;
    use CssSelectorIdentifierDataProviderTrait;
    use XpathExpressionIdentifierDataProviderTrait;
    use ElementParameterIdentifierDataProviderTrait;
    use PageElementReferenceIdentifierDataProviderTrait;
    use AttributeIdentifierDataProviderTrait;

    /**
     * @var ElementIdentifierFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = ElementIdentifierFactory::createFactory();
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xpathExpressionDataProvider
     */
    public function testHandlesDoesHandle(string $identifierString)
    {
        $this->assertTrue($this->factory->handles($identifierString));
    }

    /**
     * @dataProvider elementParameterDataProvider
     * @dataProvider pageElementReferenceDataProvider
     * @dataProvider attributeIdentifierStringDataProvider
     */
    public function testHandlesDoesNotHandle(string $identifierString)
    {
        $this->assertFalse($this->factory->handles($identifierString));
    }

    /**
     * @dataProvider cssSelectorIdentifierDataProvider
     * @dataProvider xpathExpressionIdentifierDataProvider
     */
    public function testCreateSuccess(string $identifierString, IdentifierInterface $expectedIdentifier)
    {
        $identifier = $this->factory->create($identifierString);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);
        $this->assertEquals($expectedIdentifier, $identifier);
    }

    /**
     * @dataProvider elementParameterIdentifierDataProvider
     * @dataProvider pageElementReferenceIdentifierDataProvider
     * @dataProvider attributeIdentifierDataProvider
     */
    public function testCreateReturnsNull(string $identifierString)
    {
        $this->assertNull($this->factory->create($identifierString));
    }
}
