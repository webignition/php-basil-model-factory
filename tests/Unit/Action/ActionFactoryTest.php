<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Action\NoArgumentsAction;
use webignition\BasilModel\Action\UnrecognisedAction;
use webignition\BasilModel\Action\WaitAction;
use webignition\BasilModel\Identifier\ElementIdentifier;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\EnvironmentValue;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ValueTypes;
use webignition\BasilModelFactory\Action\ActionFactory;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;

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
        $cssSelectorValue = LiteralValue::createCssSelectorValue('.selector');
        $cssSelectorIdentifier = new ElementIdentifier($cssSelectorValue);

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
                    new ElementIdentifier(
                        $cssSelectorValue,
                        3
                    ),
                    '".selector":3'
                ),
            ],
            'click page element reference' => [
                'actionString' => 'click page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'click page_import_name.elements.element_name',
                    ActionTypes::CLICK,
                    new Identifier(
                        IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                        new ObjectValue(
                            ValueTypes::PAGE_ELEMENT_REFERENCE,
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
                    new Identifier(
                        IdentifierTypes::ELEMENT_PARAMETER,
                        new ObjectValue(
                            ValueTypes::ELEMENT_PARAMETER,
                            '$elements.name',
                            'elements',
                            'name'
                        )
                    ),
                    '$elements.name'
                ),
            ],
            'click with no arguments' => [
                'actionString' => 'click',
                'expectedAction' => new InteractionAction(
                    'click',
                    ActionTypes::CLICK,
                    null,
                    ''
                ),
            ],
        ];
    }

    public function createFromActionStringForSubmitActionDataProvider(): array
    {
        $cssSelectorValue = LiteralValue::createCssSelectorValue('.selector');
        $cssSelectorIdentifier = new ElementIdentifier($cssSelectorValue);

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
                    new ElementIdentifier(
                        $cssSelectorValue,
                        3
                    ),
                    '".selector":3'
                ),
            ],
            'submit page element reference' => [
                'actionString' => 'submit page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'submit page_import_name.elements.element_name',
                    ActionTypes::SUBMIT,
                    new Identifier(
                        IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                        new ObjectValue(
                            ValueTypes::PAGE_ELEMENT_REFERENCE,
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
                    new Identifier(
                        IdentifierTypes::ELEMENT_PARAMETER,
                        new ObjectValue(
                            ValueTypes::ELEMENT_PARAMETER,
                            '$elements.name',
                            'elements',
                            'name'
                        )
                    ),
                    '$elements.name'
                ),
            ],
            'submit no arguments' => [
                'actionString' => 'submit',
                'expectedAction' => new InteractionAction(
                    'submit',
                    ActionTypes::SUBMIT,
                    null,
                    ''
                ),
            ],
        ];
    }

    public function createFromActionStringForWaitForActionDataProvider(): array
    {
        $cssSelectorValue = LiteralValue::createCssSelectorValue('.selector');
        $cssSelectorIdentifier = new ElementIdentifier($cssSelectorValue);

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
                    new ElementIdentifier(
                        $cssSelectorValue,
                        3
                    ),
                    '".selector":3'
                ),
            ],
            'wait-for page element reference' => [
                'actionString' => 'wait-for page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'wait-for page_import_name.elements.element_name',
                    ActionTypes::WAIT_FOR,
                    new Identifier(
                        IdentifierTypes::PAGE_ELEMENT_REFERENCE,
                        new ObjectValue(
                            ValueTypes::PAGE_ELEMENT_REFERENCE,
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
                    new Identifier(
                        IdentifierTypes::ELEMENT_PARAMETER,
                        new ObjectValue(
                            ValueTypes::ELEMENT_PARAMETER,
                            '$elements.name',
                            'elements',
                            'name'
                        )
                    ),
                    '$elements.name'
                ),
            ],
            'wait-for no arguments' => [
                'actionString' => 'wait-for',
                'expectedAction' => new InteractionAction(
                    'wait-for',
                    ActionTypes::WAIT_FOR,
                    null,
                    ''
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
                'expectedAction' => new WaitAction('wait 1', LiteralValue::createStringValue('1')),
            ],
            'wait 15' => [
                'actionString' => 'wait 15',
                'expectedAction' => new WaitAction('wait 15', LiteralValue::createStringValue('15')),
            ],
            'wait $data.name' => [
                'actionString' => 'wait $data.name',
                'expectedAction' => new WaitAction('wait $data.name', new ObjectValue(
                    ValueTypes::DATA_PARAMETER,
                    '$data.name',
                    'data',
                    'name'
                )),
            ],
            'wait no arguments' => [
                'actionString' => 'wait',
                'expectedAction' => new WaitAction('wait', LiteralValue::createStringValue('')),
            ],
            'wait $env.DURATION' => [
                'actionString' => 'wait $env.DURATION',
                'expectedAction' => new WaitAction('wait $env.DURATION', new EnvironmentValue(
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
        $cssSelectorValue = LiteralValue::createCssSelectorValue('.selector');
        $cssSelectorIdentifier = new ElementIdentifier($cssSelectorValue);
        $scalarValue = LiteralValue::createStringValue('value');

        return [
            'css selector, scalar value' => [
                'actionString' => 'set ".selector" to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector" to "value"',
                    $cssSelectorIdentifier,
                    $scalarValue,
                    '".selector" to "value"'
                ),
            ],
            'css selector, data parameter value' => [
                'actionString' => 'set ".selector" to $data.name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $data.name',
                    $cssSelectorIdentifier,
                    new ObjectValue(
                        ValueTypes::DATA_PARAMETER,
                        '$data.name',
                        'data',
                        'name'
                    ),
                    '".selector" to $data.name'
                ),
            ],
            'css selector, element parameter value' => [
                'actionString' => 'set ".selector" to $elements.name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $elements.name',
                    $cssSelectorIdentifier,
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        'elements',
                        'name'
                    ),
                    '".selector" to $elements.name'
                ),
            ],
            'css selector, escaped quotes scalar value' => [
                'actionString' => 'set ".selector" to "\"value\""',
                'expectedAction' => new InputAction(
                    'set ".selector" to "\"value\""',
                    $cssSelectorIdentifier,
                    LiteralValue::createStringValue('"value"'),
                    '".selector" to "\"value\""'
                ),
            ],
            'css selector includes stop words, scalar value' => [
                'actionString' => 'set ".selector to value" to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector to value" to "value"',
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.selector to value')
                    ),
                    LiteralValue::createStringValue('value'),
                    '".selector to value" to "value"'
                ),
            ],
            'xpath expression, scalar value' => [
                'actionString' => 'set "//foo" to "value"',
                'expectedAction' => new InputAction(
                    'set "//foo" to "value"',
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//foo')
                    ),
                    LiteralValue::createStringValue('value'),
                    '"//foo" to "value"'
                ),
            ],
            'xpath expression includes stopwords, scalar value' => [
                'actionString' => 'set "//a[ends-with(@href to value, \".pdf\")]" to "value"',
                'expectedAction' => new InputAction(
                    'set "//a[ends-with(@href to value, \".pdf\")]" to "value"',
                    new ElementIdentifier(
                        LiteralValue::createXpathExpressionValue('//a[ends-with(@href to value, \".pdf\")]')
                    ),
                    LiteralValue::createStringValue('value'),
                    '"//a[ends-with(@href to value, \".pdf\")]" to "value"'
                ),
            ],
            'no arguments' => [
                'actionString' => 'set',
                'expectedAction' => new InputAction(
                    'set',
                    null,
                    null,
                    ''
                ),
            ],
            'lacking value' => [
                'actionString' => 'set ".selector" to',
                'expectedAction' => new InputAction(
                    'set ".selector" to',
                    $cssSelectorIdentifier,
                    null,
                    '".selector" to'
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
                    new ElementIdentifier(
                        LiteralValue::createCssSelectorValue('.selector to value')
                    ),
                    LiteralValue::createStringValue('value'),
                    '".selector to value" "value"'
                ),
            ],
            '".selector" lacking "to" keyword and lacking value' => [
                'actionString' => 'set ".selector"',
                'expectedAction' => new InputAction(
                    'set ".selector"',
                    $cssSelectorIdentifier,
                    null,
                    '".selector"'
                ),
            ],
        ];
    }

    public function testCreateFromActionStringForUnrecognisedAction()
    {
        $actionString = 'foo ".selector" to "value';

        $action = $this->actionFactory->createFromActionString($actionString);

        $this->assertInstanceOf(UnrecognisedAction::class, $action);
        $this->assertSame('foo', $action->getType());
        $this->assertFalse($action->isRecognised());
    }

    public function testCreateFromEmptyActionString()
    {
        $actionString = '';

        $action = $this->actionFactory->createFromActionString($actionString);

        $this->assertInstanceOf(UnrecognisedAction::class, $action);
        $this->assertSame('', $action->getType());
        $this->assertFalse($action->isRecognised());
    }

    /**
     * @dataProvider createFromActionStringThrowsPageElementExceptionDataProvider
     */
    public function testCreateFromActionStringThrowsPageElementException(
        string $actionString,
        string $expectedExceptionMessage
    ) {
        $this->expectException(MalformedPageElementReferenceException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->actionFactory->createFromActionString($actionString);
    }

    public function createFromActionStringThrowsPageElementExceptionDataProvider(): array
    {
        return [
            'click malformed page element reference' => [
                'actionString' => 'click invalid-page-element-reference',
                'expectedExceptionMessage' => 'Malformed page element reference "invalid-page-element-reference"',
            ],
            'click page object property' => [
                'actionString' => 'click $page.title',
                'expectedExceptionMessage' => 'Malformed page element reference "$page.title"',
            ],
            'click browser object property' => [
                'actionString' => 'click $browser.size',
                'expectedExceptionMessage' => 'Malformed page element reference "$browser.size"',
            ],
            'click data parameter' => [
                'actionString' => 'click $data.key',
                'expectedExceptionMessage' => 'Malformed page element reference "$data.key"',
            ],
            'click environment parameter' => [
                'actionString' => 'click $env.KEY',
                'expectedExceptionMessage' => 'Malformed page element reference "$env.KEY"',
            ],
            'set malformed page element reference' => [
                'actionString' => 'set invalid-page-element-reference to "value"',
                'expectedExceptionMessage' => 'Malformed page element reference "invalid-page-element-reference"',
            ],
            'set page object property' => [
                'actionString' => 'set $page.title to "value"',
                'expectedExceptionMessage' => 'Malformed page element reference "$page.title"',
            ],
            'set browser object property' => [
                'actionString' => 'set $browser.size to "value"',
                'expectedExceptionMessage' => 'Malformed page element reference "$browser.size"',
            ],
            'set data parameter' => [
                'actionString' => 'set $data.key to "value"',
                'expectedExceptionMessage' => 'Malformed page element reference "$data.key"',
            ],
            'set environment parameter' => [
                'actionString' => 'set $env.KEY to "value"',
                'expectedExceptionMessage' => 'Malformed page element reference "$env.KEY"',
            ],
            'submit malformed page element reference' => [
                'actionString' => 'submit invalid-page-element-reference',
                'expectedExceptionMessage' => 'Malformed page element reference "invalid-page-element-reference"',
            ],
            'wait-for malformed page element reference' => [
                'actionString' => 'wait-for invalid-page-element-reference',
                'expectedExceptionMessage' => 'Malformed page element reference "invalid-page-element-reference"',
            ],
            'click css selector unquoted is treated as page model element reference' => [
                'actionString' => 'click .sign-in-form .submit-button',
                'expectedExceptionMessage' => 'Malformed page element reference ".sign-in-form .submit-button"',
            ],
            'submit css selector unquoted is treated as page model element reference' => [
                'actionString' => 'submit .sign-in-form',
                'expectedExceptionMessage' => 'Malformed page element reference ".sign-in-form"',
            ],
            'wait-for css selector unquoted is treated as page model element reference' => [
                'actionString' => 'wait-for .sign-in-form .submit-button',
                'expectedExceptionMessage' => 'Malformed page element reference ".sign-in-form .submit-button"',
            ],
        ];
    }
}
