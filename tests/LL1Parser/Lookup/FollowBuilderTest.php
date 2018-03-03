<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder;
use Remorhaz\UniLex\LL1Parser\Lookup\FollowBuilder;
use Remorhaz\UniLex\Test\LL1Parser\ExampleGrammar;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\FollowBuilder
 */
class FollowBuilderTest extends TestCase
{

    /**
     * @dataProvider providerValidGrammars
     * @param array $terminalMap
     * @param array $nonTerminalMap
     * @param int $startSymbolId
     * @param int $eofTokenId
     * @param int $symbolId
     * @param array $expectedFollow
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetFollow_ValidGrammar_ResultGetReturnsMatchingValue(
        array $terminalMap,
        array $nonTerminalMap,
        int $startSymbolId,
        int $eofTokenId,
        int $symbolId,
        array $expectedFollow
    ): void {
        $grammar = ContextFreeGrammar::loadFromMaps($terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId);
        $first = (new FirstBuilder($grammar))->getFirst();
        $follow = (new FollowBuilder($grammar, $first))->getFollow();
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
