<?php

namespace webignition\BasilModelFactory\Identifier;

use webignition\BasilModel\Identifier\IdentifierInterface;
use webignition\BasilModelFactory\MalformedPageElementReferenceException;

interface IdentifierTypeFactoryInterface
{
    public static function createFactory();
    public function handles(string $identifierString): bool;

    /**
     * @param string $identifierString
     *
     * @return IdentifierInterface|null
     *
     * @throws MalformedPageElementReferenceException
     */
    public function create(string $identifierString): ?IdentifierInterface;
}
