<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;
use webignition\BasilModel\Action\ActionTypes;
use webignition\BasilModel\Action\WaitAction;
use webignition\BasilModelFactory\ValueFactory;

class WaitActionTypeFactory implements ActionTypeFactoryInterface
{
    private $valueFactory;

    public function __construct(ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;
    }

    public static function createFactory(): WaitActionTypeFactory
    {
        return new WaitActionTypeFactory(
            ValueFactory::createFactory()
        );
    }

    public function handles(string $type): bool
    {
        return ActionTypes::WAIT === $type;
    }

    public function createForActionType(string $actionString, string $type, string $arguments): ActionInterface
    {
        $duration = $this->valueFactory->createFromValueString($arguments);

        return new WaitAction($actionString, $duration);
    }
}
