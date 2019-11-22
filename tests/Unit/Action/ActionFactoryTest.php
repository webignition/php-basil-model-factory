<?php

namespace webignition\BasilModelFactory\Tests\Unit\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\InputAction;
use webignition\BasilModel\Action\InteractionAction;
use webignition\BasilModel\Action\NoArgumentsAction;
use webignition\BasilModel\Action\WaitAction;
use webignition\BasilModel\Identifier\DomIdentifier;
use webignition\BasilModel\Identifier\ReferenceIdentifier;
use webignition\BasilModel\Value\DomIdentifierReference;
use webignition\BasilModel\Value\DomIdentifierReferenceType;
use webignition\BasilModel\Value\LiteralValue;
use webignition\BasilModel\Value\ObjectValue;
use webignition\BasilModel\Value\ObjectValueType;
use webignition\BasilModel\Value\PageElementReference;
use webignition\BasilModelFactory\Action\ActionFactory;
use webignition\BasilModelFactory\Exception\InvalidActionTypeException;
use webignition\BasilModelFactory\Exception\InvalidIdentifierStringException;
use webignition\BasilParser\ActionParser;

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
     * @dataProvider createFromActionDataForInputActionDataProvider
     * @dataProvider createFromActionDataForInteractionActionDataProvider
     * @dataProvider createFromActionDataForNoArgumentsActionDataProvider
     * @dataProvider createFromActionDataForWaitActionDataProvider
     */
    public function testCreateFromActionData(ActionData $actionData, ActionInterface $expectedAction)
    {
        $action = $this->actionFactory->createFromActionData($actionData);

        $this->assertEquals($expectedAction, $action);
    }

    public function createFromActionDataForInputActionDataProvider(): array
    {
        $actionParser = ActionParser::create();

        $elementLocator = '.selector';
        $cssSelectorIdentifier = new DomIdentifier($elementLocator);
        $scalarValue = new LiteralValue('value');

        return [
            'css element selector, scalar value' => [
                'actionData' => $actionParser->parse('set ".selector" to "value"'),
                'expectedAction' => new InputAction(
                    'set ".selector" to "value"',
                    $cssSelectorIdentifier,
                    $scalarValue,
                    '".selector" to "value"'
                ),
            ],
            'page model element reference, scalar value' => [
                'actionData' => $actionParser->parse('set page_import_name.elements.element_name to "value"'),
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
                'actionData' => $actionParser->parse('set $elements.element_name to "value"'),
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
            'css element selector, data parameter value' => [
                'actionData' => $actionParser->parse('set ".selector" to $data.name'),
                'expectedAction' => new InputAction(
                    'set ".selector" to $data.name',
                    $cssSelectorIdentifier,
                    new ObjectValue(ObjectValueType::DATA_PARAMETER, '$data.name', 'name'),
                    '".selector" to $data.name'
                ),
            ],
            'css element selector, element parameter value' => [
                'actionData' => $actionParser->parse('set ".selector" to $elements.element_name'),
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
                'actionData' => $actionParser->parse('set ".selector" to $elements.element_name.attribute_name'),
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
        ];
    }

    public function createFromActionDataForInteractionActionDataProvider(): array
    {
        $actionParser = ActionParser::create();

        $elementLocator = '.selector';
        $cssSelectorIdentifier = new DomIdentifier($elementLocator);

        return [
            'click css selector' => [
                'actionData' => $actionParser->parse('click ".selector"'),
                'expectedAction' => new InteractionAction(
                    'click ".selector"',
                    ActionTypes::CLICK,
                    $cssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'click page element reference' => [
                'actionData' => $actionParser->parse('click page_import_name.elements.element_name'),
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
                'actionData' => $actionParser->parse('click $elements.name'),
                'expectedAction' => new InteractionAction(
                    'click $elements.name',
                    ActionTypes::CLICK,
                    ReferenceIdentifier::createElementReferenceIdentifier(
                        new DomIdentifierReference(DomIdentifierReferenceType::ELEMENT, '$elements.name', 'name')
                    ),
                    '$elements.name'
                ),
            ],
            'submit css selector' => [
                'actionData' => $actionParser->parse('submit ".selector"'),
                'expectedAction' => new InteractionAction(
                    'submit ".selector"',
                    ActionTypes::SUBMIT,
                    $cssSelectorIdentifier,
                    '".selector"'
                ),
            ],
            'wait-for css selector' => [
                'actionData' => $actionParser->parse('wait-for ".selector"'),
                'expectedAction' => new InteractionAction(
                    'wait-for ".selector"',
                    ActionTypes::WAIT_FOR,
                    $cssSelectorIdentifier,
                    '".selector"'
                ),
            ],
        ];
    }

    public function createFromActionDataForNoArgumentsActionDataProvider(): array
    {
        $actionParser = ActionParser::create();

        return [
            'reload' => [
                'actionData' => $actionParser->parse('reload'),
                'expectedAction' => new NoArgumentsAction('reload', ActionTypes::RELOAD, ''),
            ],
            'back' => [
                'actionData' => $actionParser->parse('back'),
                'expectedAction' => new NoArgumentsAction('back', ActionTypes::BACK, ''),
            ],
            'forward' => [
                'actionData' => $actionParser->parse('forward'),
                'expectedAction' => new NoArgumentsAction('forward', ActionTypes::FORWARD, ''),
            ],
        ];
    }

    public function createFromActionDataForWaitActionDataProvider(): array
    {
        $actionParser = ActionParser::create();

        return [
            'wait 1' => [
                'actionData' => $actionParser->parse('wait 1'),
                'expectedAction' => new WaitAction('wait 1', new LiteralValue('1')),
            ],
            'wait $data.name' => [
                'actionData' => $actionParser->parse('wait $data.name'),
                'expectedAction' => new WaitAction('wait $data.name', new ObjectValue(
                    ObjectValueType::DATA_PARAMETER,
                    '$data.name',
                    'name'
                )),
            ],
            'wait no arguments' => [
                'actionData' => $actionParser->parse('wait'),
                'expectedAction' => new WaitAction('wait', new LiteralValue('')),
            ],
            'wait $env.DURATION' => [
                'actionData' => $actionParser->parse('wait $env.DURATION'),
                'expectedAction' => new WaitAction('wait $env.DURATION', new ObjectValue(
                    ObjectValueType::ENVIRONMENT_PARAMETER,
                    '$env.DURATION',
                    'DURATION'
                )),
            ],
        ];
    }

    /**
     * @dataProvider createFromActionStringForUnknownActionTypeDataProvider
     */
    public function testCreateFromActionDataForUnknownActionType(
        ActionData $actionData,
        string $expectedInvalidActionType
    ) {
        $this->expectExceptionObject(new InvalidActionTypeException($expectedInvalidActionType));

        $this->actionFactory->createFromActionData($actionData);
    }

    public function createFromActionStringForUnknownActionTypeDataProvider(): array
    {
        return [
            'no type' => [
                'actionData' => new ActionData('', null),
                'expectedInvalidActionType' => '',
            ],
            'unknown type' => [
                'actionData' => new ActionData('foo ".selector"', 'foo'),
                'expectedInvalidActionType' => 'foo',
            ],
        ];
    }

    /**
     * @dataProvider createThrowsInvalidIdentifierStringExceptionDataProvider
     */
    public function testCreateThrowsInvalidIdentifierStringException(
        ActionData $actionData,
        InvalidIdentifierStringException $expectedException
    ) {
        $this->expectExceptionObject($expectedException);

        $this->actionFactory->createFromActionData($actionData);
    }

    public function createThrowsInvalidIdentifierStringExceptionDataProvider()
    {
        $actionParser = ActionParser::create();

        return [
            'click with no arguments' => [
                'actionData' => $actionParser->parse('click'),
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'submit no arguments' => [
                'actionData' => $actionParser->parse('submit'),
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'wait-for no arguments' => [
                'actionData' => $actionParser->parse('wait-for'),
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'input no arguments' => [
                'actionData' => $actionParser->parse('set'),
                'expectedException' => new InvalidIdentifierStringException(''),
            ],
            'click malformed page element reference' => [
                'actionData' => $actionParser->parse('click invalid-page-element-reference'),
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'click page property' => [
                'actionData' => $actionParser->parse('click $page.title'),
                'expectedException' => new InvalidIdentifierStringException('$page.title'),
            ],
            'click browser property' => [
                'actionData' => $actionParser->parse('click $browser.size'),
                'expectedException' => new InvalidIdentifierStringException('$browser.size'),
            ],
            'click data parameter' => [
                'actionData' => $actionParser->parse('click $data.key'),
                'expectedException' => new InvalidIdentifierStringException('$data.key'),
            ],
            'click environment parameter' => [
                'actionData' => $actionParser->parse('click $env.KEY'),
                'expectedException' => new InvalidIdentifierStringException('$env.KEY'),
            ],
            'set malformed page element reference' => [
                'actionData' => $actionParser->parse('set invalid-page-element-reference to "value"'),
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'set attribute' => [
                'actionData' => $actionParser->parse('set $elements.element.attribute to "value"'),
                'expectedException' => new InvalidIdentifierStringException('$elements.element.attribute'),
            ],
            'set page property' => [
                'actionData' => $actionParser->parse('set $page.title to "value"'),
                'expectedException' => new InvalidIdentifierStringException('$page.title'),
            ],
            'set browser property' => [
                'actionData' => $actionParser->parse('set $browser.size to "value"'),
                'expectedException' => new InvalidIdentifierStringException('$browser.size'),
            ],
            'set data parameter' => [
                'actionData' => $actionParser->parse('set $data.key to "value"'),
                'expectedException' => new InvalidIdentifierStringException('$data.key'),
            ],
            'set environment parameter' => [
                'actionData' => $actionParser->parse('set $env.KEY to "value"'),
                'expectedException' => new InvalidIdentifierStringException('$env.KEY'),
            ],
            'submit malformed page element reference' => [
                'actionData' => $actionParser->parse('submit invalid-page-element-reference'),
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'wait-for malformed page element reference' => [
                'actionData' => $actionParser->parse('wait-for invalid-page-element-reference'),
                'expectedException' => new InvalidIdentifierStringException('invalid-page-element-reference'),
            ],
            'click css selector unquoted is treated as page element reference' => [
                'actionData' => $actionParser->parse('click .sign-in-form .submit-button'),
                'expectedException' => new InvalidIdentifierStringException('.sign-in-form'),
            ],
            'submit css selector unquoted is treated as page element reference' => [
                'actionData' => $actionParser->parse('submit .sign-in-form'),
                'expectedException' => new InvalidIdentifierStringException('.sign-in-form'),
            ],
            'wait-for css selector unquoted is treated as page element reference' => [
                'actionData' => $actionParser->parse('wait-for .sign-in-form .submit-button'),
                'expectedException' => new InvalidIdentifierStringException('.sign-in-form'),
            ],
        ];
    }
}
