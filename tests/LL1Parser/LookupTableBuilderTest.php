<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\CFG\Grammar;
use Remorhaz\UniLex\LL1Parser\LookupTableBuilder;

class LookupTableBuilderTest extends TestCase
{

    /**
     * @param array $terminalMap
     * @param array $nonTerminalMap
     * @param int $startSymbolId
     * @param int $eofTokenId
     * @param array $expectedTable
     * @dataProvider providerValidGrammarTables
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetFirst_ValidGrammar_ResultGetTokensReturnsMatchingValue(
        array $terminalMap,
        array $nonTerminalMap,
        int $startSymbolId,
        int $eofTokenId,
        array $expectedTable
    ): void {
        $grammar = new Grammar($terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId);
        $actualValue = (new LookupTableBuilder($grammar))->getTable()->exportMap();
        self::assertEquals($expectedTable, $actualValue);
    }

    public function providerValidGrammarTables(): array
    {
        $examples = new ExampleGrammar;
        $data = [];
        $data["Classic example 4.14 from Dragonbook"] =
            array_merge(
                $examples->getDragonBook414Grammar(),
                [$examples->getDragonBook414Table()]
            );
        return $data;
    }
}
