<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;

interface IdentifierTypeFactoryInterface
{
    public static function createFactory();
    public function handles(string $identifierString): bool;

    /**
     * @param string $identifierString
     *
     * @return IdentifierInterface|null
     */
    public function create(string $identifierString): ?IdentifierInterface;
}
