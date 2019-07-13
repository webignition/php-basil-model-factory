<?php

namespace webignition\BasilModelFactory\Tests\DataProvider;

trait QuotedIdentifierStringDataProviderTrait
{
    public function quotedIdentifierStringDataProvider(): array
    {
        return [
            'quoted: assertion: whole-word quoted identifier' => [
                'string' => '".selector" is "value"',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: assertion: quoted identifier ending with comparison' => [
                'string' => '".selector is" is "value"',
                'expectedIdentifierString' => '".selector is"',
            ],
            'quoted: assertion: quoted identifier containing comparison and value' => [
                'string' => '".selector is value" is "value"',
                'expectedIdentifierString' => '".selector is value"',
            ],
            'quoted: assertion: whole-word quoted identifier with encapsulating escaped quotes' => [
                'string' => '"\".selector\"" is "value"',
                'expectedIdentifierString' => '"\".selector\""',
            ],
            'quoted: assertion: quoted quoted identifier containing escaped quotes' => [
                'string' => '".selector \".is\"" is "value"',
                'expectedIdentifierString' => '".selector \".is\""',
            ],
            'quoted: set action arguments: whole-word selector' => [
                'string' => '".selector" to "value"',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: set action arguments: whole-word selector ending with stop word' => [
                'string' => '".selector to " to "value"',
                'expectedIdentifierString' => '".selector to "',
            ],
            'quoted: set action arguments: whole-word containing with stop word' => [
                'string' => '".selector to value" to "value"',
                'expectedIdentifierString' => '".selector to value"',
            ],
            'quoted: set action arguments: no value following stop word' => [
                'string' => '".selector" to',
                'expectedIdentifierString' => '".selector"',
            ],
            'assertion: no value following "is" keyword' => [
                'string' => '".selector" is',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: assertion: no value following "is-not" keyword' => [
                'string' => '".selector" is-not',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: assertion: no value following "includes" keyword' => [
                'string' => '".selector" includes',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: assertion: no value following "excludes" keyword' => [
                'string' => '".selector" excludes',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: assertion: no value following "matches" keyword' => [
                'string' => '".selector" matches',
                'expectedIdentifierString' => '".selector"',
            ],
            'quoted: whole-word quoted identifier only' => [
                'string' => '".selector"',
                'expectedIdentifierString' => '".selector"',
            ],
        ];
    }
}
