<?php

namespace webignition\BasilModelFactory\Tests\Unit;

use webignition\BasilDataStructure\Assertion as AssertionData;
use webignition\BasilModel\Assertion\AssertionComparison;
use webignition\BasilModel\Assertion\AssertionInterface;
use webignition\BasilModel\Assertion\ComparisonAssertion;
use webignition\BasilModel\Assertion\ExaminationAssertion;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Value\DomIdentifierValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModelFactory\AssertionFactory;
use webignition\BasilModelFactory\Exception\EmptyAssertionStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;
use webignition\BasilParser\AssertionParser;

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
     * @dataProvider createFromAssertionDataDataProvider
     */
    public function testCreateFromAssertionData(AssertionData $assertionData, AssertionInterface $expectedAssertion)
    {
        $assertion = $this->assertionFactory->createFromAssertionData($assertionData);

        $this->assertInstanceOf(AssertionInterface::class, $assertion);
        $this->assertEquals($expectedAssertion, $assertion);
    }

    public function createFromAssertionDataDataProvider(): array
    {
        $assertionParser = AssertionParser::create();

        $elementLocator = '.selector';

        $cssIdentifier = new DomIdentifier($elementLocator);
        $literalValue = new LiteralValue('value');

        $cssDomIdentifierValue = new DomIdentifierValue($cssIdentifier);

        return [
            'examination comparison: exists' => [
                'assertionData' => $assertionParser->parse('".selector" exists'),
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" exists',
                    $cssDomIdentifierValue,
                    AssertionComparison::EXISTS
                ),
            ],
            'examination comparison: not-exists' => [
                'assertionData' => $assertionParser->parse('".selector" not-exists'),
                'expectedAssertion' => new ExaminationAssertion(
                    '".selector" not-exists',
                    $cssDomIdentifierValue,
                    AssertionComparison::NOT_EXISTS
                ),
            ],
            'comparison assertion: is' => [
                'assertionData' => $assertionParser->parse('".selector" is "value"'),
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS,
                    $literalValue
                ),
            ],
            'comparison assertion: is-not' => [
                'assertionData' => $assertionParser->parse('".selector" is-not "value"'),
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" is-not "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::IS_NOT,
                    $literalValue
                ),
            ],
            'comparison assertion: includes' => [
                'assertionData' => $assertionParser->parse('".selector" includes "value"'),
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" includes "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::INCLUDES,
                    $literalValue
                ),
            ],
            'comparison assertion: excludes' => [
                'assertionData' => $assertionParser->parse('".selector" excludes "value"'),
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" excludes "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::EXCLUDES,
                    $literalValue
                ),
            ],
            'comparison assertion: matches' => [
                'assertionData' => $assertionParser->parse('".selector" matches "value"'),
                'expectedAssertion' => new ComparisonAssertion(
                    '".selector" matches "value"',
                    $cssDomIdentifierValue,
                    AssertionComparison::MATCHES,
                    $literalValue
                ),
            ],
        ];
    }

    public function testCreateFromAssertionDataForEmptyAssertion()
    {
        $this->expectException(EmptyAssertionStringException::class);

        $this->assertionFactory->createFromAssertionData(new AssertionData('', '', ''));
    }

    /**
     * @dataProvider createFromAssertionDataThrowsMissingValueExceptionDataProvider
     */
    public function testCreateFromAssertionDataThrowsMissingValueException(AssertionData $assertionData)
    {
        $this->expectException(MissingValueException::class);

        $this->assertionFactory->createFromAssertionData($assertionData);
    }

    public function createFromAssertionDataThrowsMissingValueExceptionDataProvider(): array
    {
        return [
            'css element selector, is, lacking value' => [
                'assertionData' => new AssertionData(
                    '".selector" is',
                    '".selector"',
                    'is'
                ),
            ],
            'css element selector, is-not, lacking value' => [
                'assertionData' => new AssertionData(
                    '".selector" is-not',
                    '".selector"',
                    'is-not'
                ),
            ],
            'css element selector, includes, lacking value' => [
                'assertionData' => new AssertionData(
                    '".selector" includes',
                    '".selector"',
                    'includes'
                ),
            ],
            'css element selector, excludes, lacking value' => [
                'assertionData' => new AssertionData(
                    '".selector" excludes',
                    '".selector"',
                    'excludes'
                ),
            ],
            'css element selector, matches, lacking value' => [
                'assertionData' => new AssertionData(
                    '".selector" matches',
                    '".selector"',
                    'matches'
                ),
            ],
        ];
    }
}
