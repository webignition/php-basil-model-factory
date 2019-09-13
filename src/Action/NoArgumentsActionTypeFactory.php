<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\NoArgumentsAction;
use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;

class NoArgumentsActionTypeFactory implements ActionTypeFactoryInterface
{
    public static function createFactory(): NoArgumentsActionTypeFactory
    {
        return new NoArgumentsActionTypeFactory();
    }

    public function handles(string $type): bool
    {
        return in_array($type, [
            ActionTypes::RELOAD,
            ActionTypes::BACK,
            ActionTypes::FORWARD,
        ]);
    }

    public function createForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        return new NoArgumentsAction($actionString, $type, $arguments);
    }
}
