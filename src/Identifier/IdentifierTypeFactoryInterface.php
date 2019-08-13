<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;

interface IdentifierTypeFactoryInterface
{
    public static function createFactory();
    public function handles(string $identifierString): bool;
    public function create(string $identifierString, ?string $name = null): ?IdentifierInterface;
}
