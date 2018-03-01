<?php

namespace Remorhaz\UniLex\Test\LL1Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LL1Parser\Grammar;
use Remorhaz\UniLex\LL1Parser\LookupFirstBuilder;

class LookupFirstBuilderTest extends TestCase
{

    public function testGetFirst_ValidGrammar_ResultGetReturnsMatchingValue(): void
    {
        $terminalMap = [1 => [2], 3 => [4], 5 => [6], 7 => [8]];
        $nonTerminalMap = [2 => [[4, 7]], 4 => [[5], []]];
        $grammar = new Grammar($terminalMap, $nonTerminalMap, 2, 7);
        $first = (new LookupFirstBuilder($grammar))->getFirst();
        $actualValue = $first->getProductionTokens(2);
        sort($actualValue);
        self::assertEquals([6, 8], $actualValue);
    }
}
