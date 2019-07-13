<?php

namespace webignition\BasilModelFactory\Action;

use webignition\BasilModel\Action\ActionInterface;

abstract class AbstractActionTypeFactory implements ActionTypeFactoryInterface
{
    public function handles(string $type): bool
    {
        return in_array($type, $this->getHandledActionTypes());
    }

    /**
     * @return string[]
     */
    abstract protected function getHandledActionTypes(): array;

    /**
     * @param string $actionString
     * @param string $type
     * @param string $arguments
     *
     * @return ActionInterface
     */
    abstract protected function doCreateForActionType(
        string $actionString,
        string $type,
        string $arguments
    ): ActionInterface;

    public function createForActionType(
        string $actionString,
        string $type,
        string $arguments
    ): ActionInterface {
        if (!$this->handles($type)) {
            throw new \RuntimeException('Invalid action type');
        }

        return $this->doCreateForActionType($actionString, $type, $arguments);
    }
}
