<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilModel\Action\ActionInterface;

interface ActionTypeFactoryInterface
{
    public function handles(string $type): bool;

    /**
     * @param string $actionString
     * @param string $type
     * @param string $arguments
     *
     * @return ActionInterface
     */
    public function createForActionType(string $actionString, string $type, string $arguments): ActionInterface;

    public function create(ActionData $actionData): ActionInterface;
}
