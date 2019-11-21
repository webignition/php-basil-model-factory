<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
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

    public function create(ActionData $actionData): ActionInterface
    {
        return new NoArgumentsAction(
            $actionData->getSource(),
            $actionData->getType(),
            (string) $actionData->getArguments()
        );
    }
}
