<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LL1Parser\LookupTableBuilder;

class LookupTableBuilderTest extends TestCase
{

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Empty map of terminal productions
     */
    public function testConstruct_EmptyTerminalMap_ThrowsException(): void
    {
        $terminalMap = [];
        $nonTerminalMap = [1 => [[2]]];
        new LookupTableBuilder($terminalMap, $nonTerminalMap);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Empty map of non-terminal productions
     */
    public function testConstruct_EmptyNonTerminalMap_ThrowsException(): void
    {
        $terminalMap = [1 => [1]];
        $nonTerminalMap = [];
        new LookupTableBuilder($terminalMap, $nonTerminalMap);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Productions marked both as terminals and non-terminals: 1
     */
    public function testValidateMaps_MapsWithSameKey_ThrowsException(): void
    {
        $terminalMap = [1 => [1], 2 => [2]];
        $nonTerminalMap = [1 => [[1, 2]]];
        (new LookupTableBuilder($terminalMap, $nonTerminalMap))->validateMaps();
    }
}
