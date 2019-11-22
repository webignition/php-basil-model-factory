<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilDataStructure\Action\ActionInterface as ActionDataInterface;

use webignition\BasilModel\Action\ActionInterface;

interface ActionTypeFactoryInterface
{
    public function handles(string $type): bool;

    public function create(ActionDataInterface $actionData): ActionInterface;
}
