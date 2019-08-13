<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\Identifier;
use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\ValueFactory;

abstract class AbstractValueBasedIdentifierFactory
{
    private $valueFactory;

    public function __construct(ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;
    }

    protected function createForType(string $identifierString, string $type): IdentifierInterface
    {
        return new Identifier($type, $this->valueFactory->createFromValueString($identifierString));
    }
}
