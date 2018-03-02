<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder;
use Remorhaz\UniLex\Test\LL1Parser\ExampleGrammar;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\FirstBuilder
 */
class FirstBuilderTest extends TestCase
{

    /**
     * @param array $terminalMap
     * @param array $nonTerminalMap
     * @param int $startSymbolId
     * @param int $eofTokenId
     * @param int $symbolId
     * @param array $expectedFollow
     * @dataProvider providerValidGrammarFirsts
     */
    public function testGetFirst_ValidGrammar_ResultGetTokensReturnsMatchingValue(
        array $terminalMap,
        array $nonTerminalMap,
        int $startSymbolId,
        int $eofTokenId,
        int $symbolId,
        array $expectedFollow
    ): void {
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId);
        $first = (new FirstBuilder($grammar))->getFirst();
        $actualValue = $first->getTokens($symbolId);
        sort($actualValue);
        self::assertEquals($expectedFollow, $actualValue);
    }

    public function providerValidGrammarFirsts(): array
    {
        $examples = new ExampleGrammar;
        $data = [];
        foreach ($examples->getDragonBook414Firsts() as $key => $firsts) {
            $data["Classic example 4.14 from Dragonbook: {$key}"] =
                array_merge($examples->getDragonBook414Grammar(), $firsts);
        }
        return $data;
    }

    /**
     * @param array $terminalMap
     * @param array $nonTerminalMap
     * @param int $startSymbolId
     * @param int $eofTokenId
     * @param int $symbolId
     * @param bool $expectedValue
     * @dataProvider providerValidGrammarEpsilons
     */
    public function testGetFirst_ValidGrammar_ResultHasEpsilonReturnsMatchingValue(
        array $terminalMap,
        array $nonTerminalMap,
        int $startSymbolId,
        int $eofTokenId,
        int $symbolId,
        bool $expectedValue
    ): void {
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId);
        $first = (new FirstBuilder($grammar))->getFirst();
        $actualValue = $first->productionHasEpsilon($symbolId);
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidGrammarEpsilons(): array
    {
        $examples = new ExampleGrammar;
        $data = [];
        foreach ($examples->getDragonBook414Epsilons() as $key => $epsilon) {
            $data["Classic example 4.14 from Dragonbook: {$key}"] =
                array_merge($examples->getDragonBook414Grammar(), $epsilon);
        }
        return $data;
    }
}
