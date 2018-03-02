<?php

namespace Remorhaz\UniLex\Test\Grammar;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;

class ContextFreeGrammarTest extends TestCase
{

    public function testGetTerminalMap_ConstructWithValue_ReturnsSameValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, 2, 7);
        $actualValue = $grammar->getTerminalMap();
        self::assertEquals($terminalMap, $actualValue);
    }

    public function testGetNonTerminalMap_ConstructWithValue_ReturnsSameValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, 2, 7);
        $actualValue = $grammar->getNonTerminalMap();
        self::assertEquals($nonTerminalMap, $actualValue);
    }

    public function testGetStartSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, 2, 7);
        $actualValue = $grammar->getStartSymbol();
        self::assertEquals(2, $actualValue);
    }

    public function testGetEofSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = new ContextFreeGrammar($terminalMap, $nonTerminalMap, 2, 7);
        $actualValue = $grammar->getEofToken();
        self::assertEquals(7, $actualValue);
    }
}
