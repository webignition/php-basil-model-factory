<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\Identifier\AttributeIdentifierFactory;
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

class AttributeIdentifierFactoryTest extends \PHPUnit\Framework\TestCase
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
     * @var AttributeIdentifierFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = AttributeIdentifierFactory::createFactory();
    }

    /**
     * @dataProvider attributeIdentifierStringDataProvider
     */
    public function testHandlesDoesHandle(string $identifierString)
    {
        $this->assertTrue($this->factory->handles($identifierString));
    }

    /**
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xpathExpressionDataProvider
     * @dataProvider elementParameterDataProvider
     * @dataProvider pageElementReferenceDataProvider
     */
    public function testHandlesDoesNotHandle(string $identifierString)
    {
        $this->assertFalse($this->factory->handles($identifierString));
    }

    /**
     * @dataProvider attributeIdentifierDataProvider
     */
    public function testCreateSuccess(string $identifierString, IdentifierInterface $expectedIdentifier)
    {
        $identifier = $this->factory->create($identifierString);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);
        $this->assertEquals($expectedIdentifier, $identifier);
    }

    /**
     * @dataProvider cssSelectorIdentifierDataProvider
     * @dataProvider xpathExpressionIdentifierDataProvider
     * @dataProvider elementParameterIdentifierDataProvider
     * @dataProvider pageElementReferenceIdentifierDataProvider

     */
    public function testCreateReturnsNull(string $identifierString)
    {
        $this->assertNull($this->factory->create($identifierString));
    }
}
