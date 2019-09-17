<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\Identifier\PageElementReferenceIdentifierFactory;
use webignition\BasilModelFactory\Tests\DataProvider\AttributeIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\AttributeIdentifierStringDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\CssSelectorDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\CssSelectorIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\ElementParameterDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\DomReferenceIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\PageElementReferenceDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\PageElementReferenceIdentifierDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\XpathExpressionDataProviderTrait;
use webignition\BasilModelFactory\Tests\DataProvider\XpathExpressionIdentifierDataProviderTrait;

class PageElementReferenceIdentifierFactoryTest extends \PHPUnit\Framework\TestCase
{
    use CssSelectorDataProviderTrait;
    use XpathExpressionDataProviderTrait;
    use ElementParameterDataProviderTrait;
    use PageElementReferenceDataProviderTrait;
    use AttributeIdentifierStringDataProviderTrait;
    use CssSelectorIdentifierDataProviderTrait;
    use XpathExpressionIdentifierDataProviderTrait;
    use DomReferenceIdentifierDataProviderTrait;
    use PageElementReferenceIdentifierDataProviderTrait;
    use AttributeIdentifierDataProviderTrait;

    /**
     * @var PageElementReferenceIdentifierFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = PageElementReferenceIdentifierFactory::createFactory();
    }

    /**
     * @dataProvider pageElementReferenceDataProvider
     */
    public function testHandlesDoesHandle(string $identifierString)
    {
        $this->assertTrue($this->factory->handles($identifierString));
    }

    /**
     * @dataProvider attributeIdentifierStringDataProvider
     * @dataProvider cssSelectorDataProvider
     * @dataProvider xpathExpressionDataProvider
     * @dataProvider elementParameterDataProvider
     */
    public function testHandlesDoesNotHandle(string $identifierString)
    {
        $this->assertFalse($this->factory->handles($identifierString));
    }

    /**
     * @dataProvider pageElementReferenceIdentifierDataProvider
     */
    public function testCreateSuccess(string $identifierString, IdentifierInterface $expectedIdentifier)
    {
        $identifier = $this->factory->create($identifierString);

        $this->assertInstanceOf(IdentifierInterface::class, $identifier);
        $this->assertEquals($expectedIdentifier, $identifier);
    }

    /**
     * @dataProvider attributeIdentifierDataProvider
     * @dataProvider cssSelectorIdentifierDataProvider
     * @dataProvider xpathExpressionIdentifierDataProvider
     * @dataProvider domReferenceIdentifierDataProvider
     *
     */
    public function testCreateReturnsNull(string $identifierString)
    {
        $this->assertNull($this->factory->create($identifierString));
    }
}
