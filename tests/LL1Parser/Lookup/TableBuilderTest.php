<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder;
use Remorhaz\UniLex\Test\LL1Parser\ExampleGrammar;

class TableBuilderTest extends TestCase
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
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, $startSymbolId, $eofTokenId);
        $actualValue = (new TableBuilder($grammar))->getTable()->exportMap();
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
