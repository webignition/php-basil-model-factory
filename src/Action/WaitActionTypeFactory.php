<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\WaitAction;

class WaitActionTypeFactory extends AbstractActionTypeFactory implements ActionTypeFactoryInterface
{
    public static function createFactory(): WaitActionTypeFactory
    {
        return new WaitActionTypeFactory();
    }

    protected function getHandledActionTypes(): array
    {
        return [
            ActionTypes::WAIT,
        ];
    }

    protected function doCreateForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        return new WaitAction($actionString, $arguments);
    }
}
