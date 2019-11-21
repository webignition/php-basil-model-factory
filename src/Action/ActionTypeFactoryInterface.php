<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\Action as ActionData;
use webignition\BasilModel\Action\ActionInterface;

interface ActionTypeFactoryInterface
{
    public function handles(string $type): bool;

    public function create(ActionData $actionData): ActionInterface;
}
