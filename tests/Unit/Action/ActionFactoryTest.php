<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Action\NoArgumentsAction;
use webignition\BasilModel\Action\WaitAction;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\ElementExpression;
use webignition\BasilModel\Value\ElementExpressionType;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModelFactory\Action\ActionFactory;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilModelFactory\Exception\MissingValueException;

class ActionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actionFactory = ActionFactory::createFactory();
    }

    /**
     * @dataProvider createFromActionStringForClickActionDataProvider
     * @dataProvider createFromActionStringForSubmitActionDataProvider
     * @dataProvider createFromActionStringForWaitForActionDataProvider
     */
    public function testCreateFromActionStringForInteractionAction(
        string $actionString,
        InteractionAction $expectedAction
    ) {
        $action = $this->actionFactory->createFromActionString($actionString);

        $this->assertEquals($expectedAction, $action);
    }

    public function createFromActionStringForClickActionDataProvider(): array
    {
        $cssSelectorValue = new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR);
        $cssSelectorIdentifier = new DomIdentifier($cssSelectorValue);

        return [
            'click css selector with null position double-quoted' => [
                'actionString' => 'click ".selector"',
                'expectedAction' => new InteractionAction(
                    'click ".selector"',
                    ActionTypes::CLICK,
                    $cssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'click css selector with position double-quoted' => [
                'actionString' => 'click ".selector":3',
                'expectedAction' => new InteractionAction(
                    'click ".selector":3',
                    ActionTypes::CLICK,
                    (new DomIdentifier($cssSelectorValue))->withPosition(3),
                    '".selector":3'
                ),
            ],
            'click page element reference' => [
                'actionString' => 'click page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'click page_import_name.elements.element_name',
                    ActionTypes::CLICK,
                    ReferenceIdentifier::createPageElementReferenceIdentifier(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    'page_import_name.elements.element_name'
                ),
            ],
            'click element parameter reference' => [
                'actionString' => 'click $elements.name',
                'expectedAction' => new InteractionAction(
                    'click $elements.name',
                    ActionTypes::CLICK,
                    ReferenceIdentifier::createElementReferenceIdentifier(
                        new DomIdentifierReference(DomIdentifierReferenceType::ELEMENT, '$elements.name', 'name')
                    ),
                    '$elements.name'
                ),
            ],
        ];
    }

    public function createFromActionStringForSubmitActionDataProvider(): array
    {
        $cssSelectorValue = new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR);
        $cssSelectorIdentifier = new DomIdentifier($cssSelectorValue);

        return [
            'submit css selector with null position double-quoted' => [
                'actionString' => 'submit ".selector"',
                'expectedAction' => new InteractionAction(
                    'submit ".selector"',
                    ActionTypes::SUBMIT,
                    $cssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'submit css selector with position double-quoted' => [
                'actionString' => 'submit ".selector":3',
                'expectedAction' => new InteractionAction(
                    'submit ".selector":3',
                    ActionTypes::SUBMIT,
                    (new DomIdentifier($cssSelectorValue))->withPosition(3),
                    '".selector":3'
                ),
            ],
            'submit page element reference' => [
                'actionString' => 'submit page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'submit page_import_name.elements.element_name',
                    ActionTypes::SUBMIT,
                    ReferenceIdentifier::createPageElementReferenceIdentifier(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    'page_import_name.elements.element_name'
                ),
            ],
            'submit element parameter reference' => [
                'actionString' => 'submit $elements.name',
                'expectedAction' => new InteractionAction(
                    'submit $elements.name',
                    ActionTypes::SUBMIT,
                    ReferenceIdentifier::createElementReferenceIdentifier(
                        new DomIdentifierReference(DomIdentifierReferenceType::ELEMENT, '$elements.name', 'name')
                    ),
                    '$elements.name'
                ),
            ],
        ];
    }

    public function createFromActionStringForWaitForActionDataProvider(): array
    {
        $cssSelectorValue = new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR);
        $cssSelectorIdentifier = new DomIdentifier($cssSelectorValue);

        return [
            'wait-for css selector with null position double-quoted' => [
                'actionString' => 'wait-for ".selector"',
                'expectedAction' => new InteractionAction(
                    'wait-for ".selector"',
                    ActionTypes::WAIT_FOR,
                    $cssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'wait-for css selector with position double-quoted' => [
                'actionString' => 'wait-for ".selector":3',
                'expectedAction' => new InteractionAction(
                    'wait-for ".selector":3',
                    ActionTypes::WAIT_FOR,
                    (new DomIdentifier($cssSelectorValue))->withPosition(3),
                    '".selector":3'
                ),
            ],
            'wait-for page element reference' => [
                'actionString' => 'wait-for page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'wait-for page_import_name.elements.element_name',
                    ActionTypes::WAIT_FOR,
                    ReferenceIdentifier::createPageElementReferenceIdentifier(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    'page_import_name.elements.element_name'
                ),
            ],
            'wait-for element parameter reference' => [
                'actionString' => 'wait-for $elements.name',
                'expectedAction' => new InteractionAction(
                    'wait-for $elements.name',
                    ActionTypes::WAIT_FOR,
                    ReferenceIdentifier::createElementReferenceIdentifier(
                        new DomIdentifierReference(DomIdentifierReferenceType::ELEMENT, '$elements.name', 'name')
                    ),
                    '$elements.name'
                ),
            ],
        ];
    }

    /**
     * @dataProvider createFromActionStringForWaitActionDataProvider
     */
    public function testCreateFromActionStringForWaitAction(string $actionString, WaitAction $expectedAction)
    {
        $action = $this->actionFactory->createFromActionString($actionString);

        $this->assertEquals($expectedAction, $action);
    }

    public function createFromActionStringForWaitActionDataProvider(): array
    {
        return [
            'wait 1' => [
                'actionString' => 'wait 1',
                'expectedAction' => new WaitAction('wait 1', new LiteralValue('1')),
            ],
            'wait 15' => [
                'actionString' => 'wait 15',
                'expectedAction' => new WaitAction('wait 15', new LiteralValue('15')),
            ],
            'wait $data.name' => [
                'actionString' => 'wait $data.name',
                'expectedAction' => new WaitAction('wait $data.name', new ObjectValue(
                    ObjectValueType::DATA_PARAMETER,
                    '$data.name',
                    'name'
                )),
            ],
            'wait no arguments' => [
                'actionString' => 'wait',
                'expectedAction' => new WaitAction('wait', new LiteralValue('')),
            ],
            'wait $env.DURATION' => [
                'actionString' => 'wait $env.DURATION',
                'expectedAction' => new WaitAction('wait $env.DURATION', new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.DURATION',
                    'DURATION'
                )),
            ],
        ];
    }

    /**
     * @dataProvider createFromActionStringForNoArgumentsActionDataProvider
     */
    public function testCreateFromActionStringForNoArgumentsAction(
        string $actionString,
        NoArgumentsAction $expectedAction
    ) {
        $action = $this->actionFactory->createFromActionString($actionString);

        $this->assertEquals($expectedAction, $action);
    }

    public function createFromActionStringForNoArgumentsActionDataProvider(): array
    {
        return [
            'reload' => [
                'actionString' => 'reload',
                'expectedAction' => new NoArgumentsAction('reload', ActionTypes::RELOAD, ''),
            ],
            'back' => [
                'actionString' => 'back',
                'expectedAction' => new NoArgumentsAction('back', ActionTypes::BACK, ''),
            ],
            'forward' => [
                'actionString' => 'forward',
                'expectedAction' => new NoArgumentsAction('forward', ActionTypes::FORWARD, ''),
            ],
        ];
    }

    /**
     * @dataProvider createFromActionStringForInputActionDataProvider
     */
    public function testCreateFromActionStringForInputAction(string $actionString, InputAction $expectedAction)
    {
        $action = $this->actionFactory->createFromActionString($actionString);

        $this->assertEquals($expectedAction, $action);
    }

    public function createFromActionStringForInputActionDataProvider(): array
    {
        $cssSelectorValue = new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR);
        $cssSelectorIdentifier = new DomIdentifier($cssSelectorValue);
        $cssSelectorIdentifierWithPosition1 = (new DomIdentifier(
            new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
        ))->withPosition(1);
        $scalarValue = new LiteralValue('value');

        return [
            'css element selector, scalar value' => [
                'actionString' => 'set ".selector" to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector" to "value"',
                    $cssSelectorIdentifier,
                    $scalarValue,
                    '".selector" to "value"'
                ),
            ],
            'page model element reference, scalar value' => [
                'actionString' => 'set page_import_name.elements.element_name to "value"',
                'expectedAction' => new InputAction(
                    'set page_import_name.elements.element_name to "value"',
                    ReferenceIdentifier::createPageElementReferenceIdentifier(
                        new PageElementReference(
                            'page_import_name.elements.element_name',
                            'page_import_name',
                            'element_name'
                        )
                    ),
                    $scalarValue,
                    'page_import_name.elements.element_name to "value"'
                ),
            ],
            'element parameter, scalar value' => [
                'actionString' => 'set $elements.element_name to "value"',
                'expectedAction' => new InputAction(
                    'set $elements.element_name to "value"',
                    ReferenceIdentifier::createElementReferenceIdentifier(
                        new DomIdentifierReference(
                            DomIdentifierReferenceType::ELEMENT,
                            '$elements.element_name',
                            'element_name'
                        )
                    ),
                    $scalarValue,
                    '$elements.element_name to "value"'
                ),
            ],
            'css element selector with position 1, scalar value' => [
                'actionString' => 'set ".selector":1 to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector":1 to "value"',
                    $cssSelectorIdentifierWithPosition1,
                    $scalarValue,
                    '".selector":1 to "value"'
                ),
            ],
            'css element selector with position 2, scalar value' => [
                'actionString' => 'set ".selector":2 to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector":2 to "value"',
                    (new DomIdentifier(
                        new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                    ))->withPosition(2),
                    $scalarValue,
                    '".selector":2 to "value"'
                ),
            ],
            'css element selector with position first, scalar value' => [
                'actionString' => 'set ".selector":first to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector":first to "value"',
                    $cssSelectorIdentifierWithPosition1,
                    $scalarValue,
                    '".selector":first to "value"'
                ),
            ],
            'css element selector with position last, scalar value' => [
                'actionString' => 'set ".selector":last to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector":last to "value"',
                    (new DomIdentifier(
                        new ElementExpression('.selector', ElementExpressionType::CSS_SELECTOR)
                    ))->withPosition(-1),
                    $scalarValue,
                    '".selector":last to "value"'
                ),
            ],
            'css element selector, data parameter value' => [
                'actionString' => 'set ".selector" to $data.name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $data.name',
                    $cssSelectorIdentifier,
                    new ObjectValue(ObjectValueType::DATA_PARAMETER, '$data.name', 'name'),
                    '".selector" to $data.name'
                ),
            ],
            'css element selector, element parameter value' => [
                'actionString' => 'set ".selector" to $elements.element_name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $elements.element_name',
                    $cssSelectorIdentifier,
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ELEMENT,
                        '$elements.element_name',
                        'element_name'
                    ),
                    '".selector" to $elements.element_name'
                ),
            ],
            'css element selector, attribute parameter value' => [
                'actionString' => 'set ".selector" to $elements.element_name.attribute_name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $elements.element_name.attribute_name',
                    $cssSelectorIdentifier,
                    new DomIdentifierReference(
                        DomIdentifierReferenceType::ATTRIBUTE,
                        '$elements.element_name.attribute_name',
                        'element_name.attribute_name'
                    ),
                    '".selector" to $elements.element_name.attribute_name'
                ),
            ],
            'css element selector, escaped quotes scalar value' => [
                'actionString' => 'set ".selector" to "\"value\""',
                'expectedAction' => new InputAction(
                    'set ".selector" to "\"value\""',
                    $cssSelectorIdentifier,
                    new LiteralValue('"value"'),
                    '".selector" to "\"value\""'
                ),
            ],
            'css element selector includes stop words, scalar value' => [
                'actionString' => 'set ".selector to value" to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector to value" to "value"',
                    new DomIdentifier(
                        new ElementExpression('.selector to value', ElementExpressionType::CSS_SELECTOR)
                    ),
                    new LiteralValue('value'),
                    '".selector to value" to "value"'
                ),
            ],
            'xpath expression, scalar value' => [
                'actionString' => 'set "//foo" to "value"',
                'expectedAction' => new InputAction(
                    'set "//foo" to "value"',
                    new DomIdentifier(
                        new ElementExpression('//foo', ElementExpressionType::XPATH_EXPRESSION)
                    ),
                    new LiteralValue('value'),
                    '"//foo" to "value"'
                ),
            ],
            'xpath expression includes stopwords, scalar value' => [
                'actionString' => 'set "//a[ends-with(@href to value, \".pdf\")]" to "value"',
                'expectedAction' => new InputAction(
                    'set "//a[ends-with(@href to value, \".pdf\")]" to "value"',
                    new DomIdentifier(
                        new ElementExpression(
                            '//a[ends-with(@href to value, \".pdf\")]',
                            ElementExpressionType::XPATH_EXPRESSION
                        )
                    ),
                    new LiteralValue('value'),
                    '"//a[ends-with(@href to value, \".pdf\")]" to "value"'
                ),
            ],
            '".selector" lacking "to" keyword' => [
                'actionString' => 'set ".selector" "value"',
                'expectedAction' => new InputAction(
                    'set ".selector" "value"',
                    $cssSelectorIdentifier,
                    $scalarValue,
                    '".selector" "value"'
                ),
            ],
            '".selector to value" lacking "to" keyword' => [
                'actionString' => 'set ".selector to value" "value"',
                'expectedAction' => new InputAction(
                    'set ".selector to value" "value"',
                    new DomIdentifier(
                        new ElementExpression('.selector to value', ElementExpressionType::CSS_SELECTOR)
                    ),
                    new LiteralValue('value'),
                    '".selector to value" "value"'
                ),
            ],
        ];
    }

    /**
     * @dataProvider createFromActionStringForUnknownActionTypeDataProvider
     */
    public function testCreateFromActionStringForUnknownActionType(
        string $actionString,
        string $expectedInvalidActionType
    ) {
        $this->expectExceptionObject(new InvalidActionTypeException($expectedInvalidActionType));

        $this->actionFactory->createFromActionString($actionString);
    }

    public function createFromActionStringForUnknownActionTypeDataProvider(): array
    {
        return [
            'no type' => [
                'actionString' => '',
                'expectedInvalidActionType' => '',
            ],
            'unknown type' => [
                'actionString' => 'foo ".selector"',
                'expectedInvalidActionType' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider createFromActionStringThrowsMissingValueExceptionDataProvider
     */
    public function testCreateFromActionStringThrowsMissingValueException(string $actionString)
    {
        $this->expectException(MissingValueException::class);

        $this->actionFactory->createFromActionString($actionString);
    }

    public function createFromActionStringThrowsMissingValueExceptionDataProvider(): array
    {
        return [
            'lacking value' => [
                'actionString' => 'set ".selector" to',
            ],
            '".selector" lacking "to" keyword and lacking value' => [
                'actionString' => 'set ".selector"',
            ],
        ];
    }

    /**
     * @dataProvider createThrowsInvalidIdentifierStringExceptionDataProvider
     */
    public function testCreateThrowsInvalidIdentifierStringException(
        string $actionString,
        InvalidIdentifierStringException $expectedException
    ) {
        $this->expectExceptionObject($expectedException);

        $this->actionFactory->createFromActionString($actionString);
    }

    public function createThrowsInvalidIdentifierStringExceptionDataProvider()
    {
        return [
            'click with no arguments' => [
                'actionString' => 'click',
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'submit no arguments' => [
                'actionString' => 'submit',
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'wait-for no arguments' => [
                'actionString' => 'wait-for',
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'input no arguments' => [
                'actionString' => 'set',
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'click malformed page element reference' => [
                'actionString' => 'click invalid-page-element-reference',
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'click page property' => [
                'actionString' => 'click $page.title',
                'expectedException' => new InvalidIdentifierStringException('$page.title'),
            ],
            'click browser property' => [
                'actionString' => 'click $browser.size',
                'expectedException' => new InvalidIdentifierStringException('$browser.size'),
            ],
            'click data parameter' => [
                'actionString' => 'click $data.key',
                'expectedException' => new InvalidIdentifierStringException('$data.key'),
            ],
            'click environment parameter' => [
                'actionString' => 'click $env.KEY',
                'expectedException' => new InvalidIdentifierStringException('$env.KEY'),
            ],
            'set malformed page element reference' => [
                'actionString' => 'set invalid-page-element-reference to "value"',
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'set attribute' => [
                'actionString' => 'set $elements.element.attribute to "value"',
                'expectedException' => new InvalidIdentifierStringException('$elements.element.attribute'),
            ],
            'set page property' => [
                'actionString' => 'set $page.title to "value"',
                'expectedException' => new InvalidIdentifierStringException('$page.title'),
            ],
            'set browser property' => [
                'actionString' => 'set $browser.size to "value"',
                'expectedException' => new InvalidIdentifierStringException('$browser.size'),
            ],
            'set data parameter' => [
                'actionString' => 'set $data.key to "value"',
                'expectedException' => new InvalidIdentifierStringException('$data.key'),
            ],
            'set environment parameter' => [
                'actionString' => 'set $env.KEY to "value"',
                'expectedException' => new InvalidIdentifierStringException('$env.KEY'),
            ],
            'submit malformed page element reference' => [
                'actionString' => 'submit invalid-page-element-reference',
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'wait-for malformed page element reference' => [
                'actionString' => 'wait-for invalid-page-element-reference',
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'click css selector unquoted is treated as page element reference' => [
                'actionString' => 'click .sign-in-form .submit-button',
                'expectedException' => new InvalidIdentifierStringException('.sign-in-form .submit-button'),
            ],
            'submit css selector unquoted is treated as page element reference' => [
                'actionString' => 'submit .sign-in-form',
                'expectedException' => new InvalidIdentifierStringException('.sign-in-form'),
            ],
            'wait-for css selector unquoted is treated as page element reference' => [
                'actionString' => 'wait-for .sign-in-form .submit-button',
                'expectedException' => new InvalidIdentifierStringException('.sign-in-form .submit-button'),
            ],
        ];
    }
}
