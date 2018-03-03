<?php

namespace Remorhaz\UniLex\Test\Grammar;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;

class ContextFreeGrammarTest extends TestCase
{

    public function testGetStartSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = ContextFreeGrammar::loadFromMaps($terminalMap, $nonTerminalMap, 2, 7);
        $actualValue = $grammar->getStartSymbol();
        self::assertEquals(2, $actualValue);
    }

    public function testGetEofSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = ContextFreeGrammar::loadFromMaps($terminalMap, $nonTerminalMap, 2, 7);
        $actualValue = $grammar->getEoiSymbol();
        self::assertEquals(7, $actualValue);
    }
}
