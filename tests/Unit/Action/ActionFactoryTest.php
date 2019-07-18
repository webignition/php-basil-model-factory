<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Action\NoArgumentsAction;
use webignition\BasilModel\Action\UnrecognisedAction;
use webignition\BasilModel\Action\WaitAction;
use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierTypes;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\Value;
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
        $simpleCssSelectorValue = new Value(ValueTypes::STRING, '.selector');
        $simpleCssSelectorIdentifier = new Identifier(IdentifierTypes::CSS_SELECTOR, $simpleCssSelectorValue);

        return [
            'click css selector with null position double-quoted' => [
                'actionString' => 'click ".selector"',
                'expectedAction' => new InteractionAction(
                    'click ".selector"',
                    ActionTypes::CLICK,
                    $simpleCssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'click css selector with position double-quoted' => [
                'actionString' => 'click ".selector":3',
                'expectedAction' => new InteractionAction(
                    'click ".selector":3',
                    ActionTypes::CLICK,
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        $simpleCssSelectorValue,
                        3
                    ),
                    '".selector":3'
                ),
            ],
            'click page model reference' => [
                'actionString' => 'click page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'click page_import_name.elements.element_name',
                    ActionTypes::CLICK,
                    new Identifier(
                        IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                        new Value(
                            ValueTypes::PAGE_MODEL_REFERENCE,
                            'page_import_name.elements.element_name'
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
        $simpleCssSelectorValue = new Value(ValueTypes::STRING, '.selector');
        $simpleCssSelectorIdentifier = new Identifier(IdentifierTypes::CSS_SELECTOR, $simpleCssSelectorValue);

        return [
            'submit css selector with null position double-quoted' => [
                'actionString' => 'submit ".selector"',
                'expectedAction' => new InteractionAction(
                    'submit ".selector"',
                    ActionTypes::SUBMIT,
                    $simpleCssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'submit css selector with position double-quoted' => [
                'actionString' => 'submit ".selector":3',
                'expectedAction' => new InteractionAction(
                    'submit ".selector":3',
                    ActionTypes::SUBMIT,
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        $simpleCssSelectorValue,
                        3
                    ),
                    '".selector":3'
                ),
            ],
            'submit page model reference' => [
                'actionString' => 'submit page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'submit page_import_name.elements.element_name',
                    ActionTypes::SUBMIT,
                    new Identifier(
                        IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                        new Value(
                            ValueTypes::PAGE_MODEL_REFERENCE,
                            'page_import_name.elements.element_name'
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
        $simpleCssSelectorValue = new Value(ValueTypes::STRING, '.selector');
        $simpleCssSelectorIdentifier = new Identifier(IdentifierTypes::CSS_SELECTOR, $simpleCssSelectorValue);

        return [
            'wait-for css selector with null position double-quoted' => [
                'actionString' => 'wait-for ".selector"',
                'expectedAction' => new InteractionAction(
                    'wait-for ".selector"',
                    ActionTypes::WAIT_FOR,
                    $simpleCssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'wait-for css selector with position double-quoted' => [
                'actionString' => 'wait-for ".selector":3',
                'expectedAction' => new InteractionAction(
                    'wait-for ".selector":3',
                    ActionTypes::WAIT_FOR,
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        $simpleCssSelectorValue,
                        3
                    ),
                    '".selector":3'
                ),
            ],
            'wait-for page model reference' => [
                'actionString' => 'wait-for page_import_name.elements.element_name',
                'expectedAction' => new InteractionAction(
                    'wait-for page_import_name.elements.element_name',
                    ActionTypes::WAIT_FOR,
                    new Identifier(
                        IdentifierTypes::PAGE_MODEL_ELEMENT_REFERENCE,
                        new Value(
                            ValueTypes::PAGE_MODEL_REFERENCE,
                            'page_import_name.elements.element_name'
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
                'expectedAction' => new WaitAction('wait 1', '1'),
            ],
            'wait 15' => [
                'actionString' => 'wait 15',
                'expectedAction' => new WaitAction('wait 15', '15'),
            ],
            'wait $data.name' => [
                'actionString' => 'wait $data.name',
                'expectedAction' => new WaitAction('wait $data.name', '$data.name'),
            ],
            'wait no arguments' => [
                'actionString' => 'wait',
                'expectedAction' => new WaitAction('wait', ''),
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
        $simpleCssSelectorValue = new Value(ValueTypes::STRING, '.selector');
        $simpleCssSelectorIdentifier = new Identifier(IdentifierTypes::CSS_SELECTOR, $simpleCssSelectorValue);
        $simpleScalarValue = new Value(ValueTypes::STRING, 'value');

        return [
            'simple css selector, scalar value' => [
                'actionString' => 'set ".selector" to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector" to "value"',
                    $simpleCssSelectorIdentifier,
                    $simpleScalarValue,
                    '".selector" to "value"'
                ),
            ],
            'simple css selector, data parameter value' => [
                'actionString' => 'set ".selector" to $data.name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $data.name',
                    $simpleCssSelectorIdentifier,
                    new ObjectValue(
                        ValueTypes::DATA_PARAMETER,
                        '$data.name',
                        'data',
                        'name'
                    ),
                    '".selector" to $data.name'
                ),
            ],
            'simple css selector, element parameter value' => [
                'actionString' => 'set ".selector" to $elements.name',
                'expectedAction' => new InputAction(
                    'set ".selector" to $elements.name',
                    $simpleCssSelectorIdentifier,
                    new ObjectValue(
                        ValueTypes::ELEMENT_PARAMETER,
                        '$elements.name',
                        'elements',
                        'name'
                    ),
                    '".selector" to $elements.name'
                ),
            ],
            'simple css selector, escaped quotes scalar value' => [
                'actionString' => 'set ".selector" to "\"value\""',
                'expectedAction' => new InputAction(
                    'set ".selector" to "\"value\""',
                    $simpleCssSelectorIdentifier,
                    new Value(
                        ValueTypes::STRING,
                        '"value"'
                    ),
                    '".selector" to "\"value\""'
                ),
            ],
            'css selector includes stop words, scalar value' => [
                'actionString' => 'set ".selector to value" to "value"',
                'expectedAction' => new InputAction(
                    'set ".selector to value" to "value"',
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '.selector to value'
                        )
                    ),
                    new Value(
                        ValueTypes::STRING,
                        'value'
                    ),
                    '".selector to value" to "value"'
                ),
            ],
            'simple xpath expression, scalar value' => [
                'actionString' => 'set "//foo" to "value"',
                'expectedAction' => new InputAction(
                    'set "//foo" to "value"',
                    new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//foo'
                        )
                    ),
                    new Value(
                        ValueTypes::STRING,
                        'value'
                    ),
                    '"//foo" to "value"'
                ),
            ],
            'xpath expression includes stopwords, scalar value' => [
                'actionString' => 'set "//a[ends-with(@href to value, \".pdf\")]" to "value"',
                'expectedAction' => new InputAction(
                    'set "//a[ends-with(@href to value, \".pdf\")]" to "value"',
                    new Identifier(
                        IdentifierTypes::XPATH_EXPRESSION,
                        new Value(
                            ValueTypes::STRING,
                            '//a[ends-with(@href to value, \".pdf\")]'
                        )
                    ),
                    new Value(
                        ValueTypes::STRING,
                        'value'
                    ),
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
                    $simpleCssSelectorIdentifier,
                    null,
                    '".selector" to'
                ),
            ],
            '".selector" lacking "to" keyword' => [
                'actionString' => 'set ".selector" "value"',
                'expectedAction' => new InputAction(
                    'set ".selector" "value"',
                    $simpleCssSelectorIdentifier,
                    $simpleScalarValue,
                    '".selector" "value"'
                ),
            ],
            '".selector to value" lacking "to" keyword' => [
                'actionString' => 'set ".selector to value" "value"',
                'expectedAction' => new InputAction(
                    'set ".selector to value" "value"',
                    new Identifier(
                        IdentifierTypes::CSS_SELECTOR,
                        new Value(
                            ValueTypes::STRING,
                            '.selector to value'
                        )
                    ),
                    new Value(
                        ValueTypes::STRING,
                        'value'
                    ),
                    '".selector to value" "value"'
                ),
            ],
            '".selector" lacking "to" keyword and lacking value' => [
                'actionString' => 'set ".selector"',
                'expectedAction' => new InputAction(
                    'set ".selector"',
                    $simpleCssSelectorIdentifier,
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
            'set malformed page element reference' => [
                'actionString' => 'set invalid-page-element-reference to "value"',
                'expectedExceptionMessage' => 'Malformed page element reference "invalid-page-element-reference"',
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
