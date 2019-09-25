<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilModel\Assertion\AssertableComparisonAssertion;
use webignition\BasilModel\Assertion\AssertableExaminationAssertion;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\Assertion\AssertableExaminedValue;
use webignition\BasilModel\Value\Assertion\AssertableExpectedValue;
use webignition\BasilModel\Value\Assertion\ExaminedValue;
use webignition\BasilModel\Value\Assertion\ExpectedValue;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\DomIdentifierValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModelFactory\AssertionFactory;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;

class AssertionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AssertionFactory
     */
    private $assertionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertionFactory = AssertionFactory::createFactory();
    }

    /**
     * @dataProvider createFromAssertionString
     */
    public function testCreateFromAssertionString(string $assertionString, AssertionInterface $expectedAssertion)
    {
        $assertion = $this->assertionFactory->createFromAssertionString($assertionString);

        $this->assertInstanceOf(AssertionInterface::class, $assertion);
        $this->assertEquals($expectedAssertion, $assertion);
    }

    public function createFromAssertionString(): array
    {
        $elementLocator = '.selector';
        $elementLocatorWithParentReference = '{{ reference }} .selector';

        $cssIdentifier = new DomIdentifier($elementLocator);
        $cssIdentifierPosition1 = $cssIdentifier->withOrdinalPosition(1);
        $cssIdentifierPosition2 = $cssIdentifier->withOrdinalPosition(2);
        $cssIdentifierPositionMinus1 = $cssIdentifier->withOrdinalPosition(-1);
        $cssIdentifierWithElementReference = new DomIdentifier($elementLocatorWithParentReference);

        $literalValue = new LiteralValue('value');

        $cssDomIdentifierValue = new DomIdentifierValue($cssIdentifier);

        $cssIdentifierValueWithAttribute = new DomIdentifierValue(
            $cssIdentifier->withAttributeName('attribute_name')
        );

        $cssIdentifierValuePosition1WithAttribute = new DomIdentifierValue(
            $cssIdentifierPosition1->withAttributeName('attribute_name')
        );

        $cssIdentifierValuePosition2WithAttribute = new DomIdentifierValue(
            $cssIdentifierPosition2->withAttributeName('attribute_name')
        );

        $cssIdentifierPositionMinus1WithAttribute = new DomIdentifierValue(
            $cssIdentifierPositionMinus1->withAttributeName('attribute_name')
        );

        $cssIdentifierExaminedValue = new ExaminedValue($cssDomIdentifierValue);
        $literalExpectedValue = new ExpectedValue($literalValue);

        $cssIdentifierPosition1ExaminedValue = new ExaminedValue(new DomIdentifierValue($cssIdentifierPosition1));
        $cssIdentifierPosition2ExaminedValue = new ExaminedValue(new DomIdentifierValue($cssIdentifierPosition2));
        $cssIdentifierPositionMinus1ExaminedValue = new ExaminedValue(
            new DomIdentifierValue($cssIdentifierPositionMinus1)
        );
        $cssSelectorWithElementReferenceExaminedValue = new ExaminedValue(
            new DomIdentifierValue($cssIdentifierWithElementReference)
        );

        $attributeValueExaminedValue = new ExaminedValue($cssIdentifierValueWithAttribute);
        $attributeValueWithPosition1ExaminedValue = new ExaminedValue($cssIdentifierValuePosition1WithAttribute);
        $attributeValueWithPosition2ExaminedValue = new ExaminedValue($cssIdentifierValuePosition2WithAttribute);
        $attributeValueWithPositionMinus1ExaminedValue = new ExaminedValue(
            $cssIdentifierPositionMinus1WithAttribute
        );

        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "value"',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1 is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":1 is "value"',
                    $cssIdentifierPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2 is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":2 is "value"',
                    $cssIdentifierPosition2ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":first is "value"',
                    $cssIdentifierPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":last is "value"',
                    $cssIdentifierPositionMinus1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '".selector".attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector".attribute_name is "value"',
                    $attributeValueExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position 1, is, scalar value' => [
                'assertionString' => '".selector":1.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":1.attribute_name is "value"',
                    $attributeValueWithPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position 2, is, scalar value' => [
                'assertionString' => '".selector":2.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":2.attribute_name is "value"',
                    $attributeValueWithPosition2ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position first, is, scalar value' => [
                'assertionString' => '".selector":first.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":first.attribute_name is "value"',
                    $attributeValueWithPosition1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css attribute selector with position last, is, scalar value' => [
                'assertionString' => '".selector":last.attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector":last.attribute_name is "value"',
                    $attributeValueWithPositionMinus1ExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector with element reference, is, scalar value' => [
                'assertionString' => '"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"{{ reference }} .selector" is "value"',
                    $cssSelectorWithElementReferenceExaminedValue,
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'css element selector, is, data parameter value' => [
                'assertionString' => '".selector" is $data.name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $data.name',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new ObjectValue(ObjectValueType::DATA_PARAMETER, '$data.name', 'name')
                    )
                ),
            ],
            'css element selector, is, element parameter' => [
                'actionString' => '".selector" is $elements.name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $elements.name',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new DomIdentifierReference(
                            DomIdentifierReferenceType::ELEMENT,
                            '$elements.name',
                            'name'
                        )
                    )
                ),
            ],
            'css element selector, is, page property' => [
                'actionString' => '".selector" is $page.url',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $page.url',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url')
                    )
                ),
            ],
            'css element selector, is, browser property' => [
                'actionString' => '".selector" is $browser.size',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $browser.size',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new ObjectValue(ObjectValueType::BROWSER_PROPERTY, '$browser.size', 'size')
                    )
                ),
            ],
            'css element selector, is, attribute parameter' => [
                'actionString' => '".selector" is $elements.element_name.attribute_name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is $elements.element_name.attribute_name',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new DomIdentifierReference(
                            DomIdentifierReferenceType::ATTRIBUTE,
                            '$elements.element_name.attribute_name',
                            'element_name.attribute_name'
                        )
                    )
                ),
            ],
            'css attribute selector,  is, attribute value' => [
                'actionString' => '".selector".data-heading-title is $elements.element_name.attribute_name',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector".data-heading-title is $elements.element_name.attribute_name',
                    new ExaminedValue(
                        new DomIdentifierValue(
                            (new DomIdentifier($elementLocator))->withAttributeName('data-heading-title')
                        )
                    ),
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new DomIdentifierReference(
                            DomIdentifierReferenceType::ATTRIBUTE,
                            '$elements.element_name.attribute_name',
                            'element_name.attribute_name'
                        )
                    )
                ),
            ],
            'css element selector, is, escaped quotes scalar value' => [
                'assertionString' => '".selector" is "\"value\""',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "\"value\""',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS,
                    new ExpectedValue(
                        new LiteralValue('"value"')
                    )
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is-not "value"',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::IS_NOT,
                    $literalExpectedValue
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css element selector, exists, scalar value is ignored' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists "value"',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css element selector, exists, data parameter value is ignored' => [
                'assertionString' => '".selector" exists $data.name',
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists $data.name',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" includes "value"',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::INCLUDES,
                    $literalExpectedValue
                ),
            ],
            'css element selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" excludes "value"',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::EXCLUDES,
                    $literalExpectedValue
                ),
            ],
            'css element selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" matches "value"',
                    $cssIdentifierExaminedValue,
                    AssertionComparison::MATCHES,
                    $literalExpectedValue
                ),
            ],
            'comparison-including css element selector, is, scalar value' => [
                'assertionString' => '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector is is-not exists not-exists includes excludes matches foo" is "value"',
                    new ExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('.selector is is-not exists not-exists includes excludes matches foo')
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'simple xpath expression, is, scalar value' => [
                'assertionString' => '"//foo" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"//foo" is "value"',
                    new ExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('//foo')
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'comparison-including non-simple xpath expression, is, scalar value' => [
                'assertionString' =>
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]" is "value"',
                    new ExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier(
                                '//a[ends-with(@href is exists not-exists matches includes excludes, \".pdf\")]'
                            )
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'page element reference, is, scalar value' => [
                'assertionString' => 'page_import_name.elements.element_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    'page_import_name.elements.element_name is "value"',
                    new ExaminedValue(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'element parameter, is, scalar value' => [
                'actionString' => '$elements.name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$elements.name is "value"',
                    new ExaminedValue(
                        new DomIdentifierReference(
                            DomIdentifierReferenceType::ELEMENT,
                            '$elements.name',
                            'name'
                        )
                    ),
                    AssertionComparison::IS,
                    $literalExpectedValue
                ),
            ],
            'page object parameter, is, scalar value' => [
                'actionString' => '$page.url is "http://example.com/"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$page.url is "http://example.com/"',
                    new ExaminedValue(new ObjectValue(ObjectValueType::PAGE_PROPERTY, '$page.url', 'url')),
                    AssertionComparison::IS,
                    new ExpectedValue(new LiteralValue('http://example.com/'))
                ),
            ],
            'browser object parameter, is, scalar value' => [
                'actionString' => '$browser.size is 1024,768',
                'expectedAssertion' => new ComparisonAssertion(
                    '$browser.size is 1024,768',
                    new ExaminedValue(new ObjectValue(ObjectValueType::BROWSER_PROPERTY, '$browser.size', 'size')),
                    AssertionComparison::IS,
                    new ExpectedValue(new LiteralValue('1024,768'))
                ),
            ],
        ];
    }

    public function testCreateFromEmptyAssertionString()
    {
        $this->expectException(EmptyAssertionStringException::class);

        $this->assertionFactory->createFromAssertionString('');
    }

    /**
     * @dataProvider createAssertableAssertionReturnsAssertionDataProvider
     */
    public function testCreateAssertableAssertionReturnsAssertion(AssertionInterface $assertion)
    {
        $this->assertSame(
            $assertion,
            $this->assertionFactory->createAssertableAssertion($assertion)
        );
    }

    public function createAssertableAssertionReturnsAssertionDataProvider(): array
    {
        return [
            'non-examination, non-comparison' => [
                'assertion' => \Mockery::mock(AssertionInterface::class),
            ],
        ];
    }

    /**
     * @dataProvider createAssertableAssertionDataProvider
     */
    public function testCreateAssertableAssertion(
        AssertionInterface $assertion,
        AssertionInterface $expectedAssertion
    ) {
        $assertableAssertion = $this->assertionFactory->createAssertableAssertion($assertion);

        $this->assertNotSame($assertion, $assertableAssertion);
        $this->assertEquals($expectedAssertion, $assertableAssertion);
    }

    public function createAssertableAssertionDataProvider(): array
    {
        return [
            'examination assertion' => [
                'assertion' => new ExaminationAssertion(
                    '".selector" exists',
                    new ExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('.selector')
                        )
                    ),
                    AssertionComparison::EXISTS
                ),
                'expectedAssertion' => new AssertableExaminationAssertion(
                    '".selector" exists',
                    new AssertableExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('.selector')
                        )
                    ),
                    AssertionComparison::EXISTS
                )
            ],
            'comparison assertion' => [
                'assertion' => new ComparisonAssertion(
                    '".selector" is "foo"',
                    new ExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('.selector')
                        )
                    ),
                    AssertionComparison::EXISTS,
                    new ExpectedValue(
                        new LiteralValue('foo')
                    )
                ),
                'expectedAssertion' => new AssertableComparisonAssertion(
                    '".selector" is "foo"',
                    new AssertableExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('.selector')
                        )
                    ),
                    AssertionComparison::EXISTS,
                    new AssertableExpectedValue(
                        new LiteralValue('foo')
                    )
                )
            ],
        ];
    }

    /**
     * @dataProvider createFromAssertionStringThrowsMissingValueExceptionDataProvider
     */
    public function testCreateFromAssertionStringThrowsMissingValueException(string $assertionString)
    {
        $this->expectException(MissingValueException::class);

        $this->assertionFactory->createFromAssertionString($assertionString);
    }

    public function createFromAssertionStringThrowsMissingValueExceptionDataProvider(): array
    {
        return [
            'css element selector, is, lacking value' => [
                'assertionString' => '".selector" is',
            ],
            'css element selector, is-not, lacking value' => [
                'assertionString' => '".selector" is-not',
            ],
            'css element selector, includes, lacking value' => [
                'assertionString' => '".selector" includes',
            ],
            'css element selector, excludes, lacking value' => [
                'assertionString' => '".selector" excludes',
            ],
            'css element selector, matches, lacking value' => [
                'assertionString' => '".selector" matches',
            ],
        ];
    }

    /**
     * @dataProvider createAssertableAssertionFromStringDataProvider
     */
    public function testCreateAssertableAssertionFromString(
        string $assertionString,
        AssertionInterface $expectedAssertion
    ) {
        $this->assertEquals(
            $expectedAssertion,
            $this->assertionFactory->createAssertableAssertionFromString($assertionString)
        );
    }

    public function createAssertableAssertionFromStringDataProvider(): array
    {
        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new AssertableComparisonAssertion(
                    '".selector" is "value"',
                    new AssertableExaminedValue(
                        new DomIdentifierValue(
                            new DomIdentifier('.selector')
                        )
                    ),
                    AssertionComparison::IS,
                    new AssertableExpectedValue(
                        new LiteralValue('value')
                    )
                ),
            ],
        ];
    }
}
