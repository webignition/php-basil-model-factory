<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\NoArgumentsAction;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;

class NoArgumentsActionTypeFactory extends AbstractActionTypeFactory implements ActionTypeFactoryInterface
{
    public static function createFactory(): NoArgumentsActionTypeFactory
    {
        return new NoArgumentsActionTypeFactory();
    }

    protected function getHandledActionTypes(): array
    {
        return [
            ActionTypes::RELOAD,
            ActionTypes::BACK,
            ActionTypes::FORWARD,
        ];
    }

    protected function doCreateForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        return new NoArgumentsAction($actionString, $type, $arguments);
    }
}
