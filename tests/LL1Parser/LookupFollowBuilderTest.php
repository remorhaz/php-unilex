<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LL1Parser\Grammar;
use Remorhaz\UniLex\LL1Parser\LookupFirstBuilder;
use Remorhaz\UniLex\LL1Parser\LookupFollowBuilder;

class LookupFollowBuilderTest extends TestCase
{

    /**
     * @dataProvider providerValidGrammars
     * @param array $terminalMap
     * @param array $nonTerminalMap
     * @param int $startSymbolId
     * @param int $eofTokenId
     * @param int $symbolId
     * @param array $expectedFollow
     */
    public function testGetFollow_ValidGrammar_ResultGetReturnsMatchingValue(
        array $terminalMap,
        array $nonTerminalMap,
        int $startSymbolId,
        int $eofTokenId,
        int $symbolId,
        array $expectedFollow
    ): void {
        $grammar = new Grammar($terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId);
        $first = (new LookupFirstBuilder($grammar))->getFirst();
        $follow = (new LookupFollowBuilder($grammar, $first))->getFollow();
        $actualValue = $follow->getTokens($symbolId);
        sort($actualValue);
        self::assertEquals($expectedFollow, $actualValue);
    }

    public function providerValidGrammars(): array
    {
        $examples = new ExampleGrammar;
        $data = [];
        foreach ($examples->getDragonBook414Follows() as $key => $follows) {
            $data["Classic example 4.14 from Dragonbook: {$key}"] =
                array_merge($examples->getDragonBook414Grammar(), $follows);
        }
        return $data;
    }
}
