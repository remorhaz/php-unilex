<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\FSM\SymbolTable;

class SymbolTableTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddSymbol_NoSymbolAdded_ReturnsZero(): void
    {
        $actualValue = (new SymbolTable)->addSymbol(RangeSet::import([1, 2]));
        self::assertSame(0, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddSymbol_SymbolAdded_ReturnsValueGreaterThanZero(): void
    {
        $table = new SymbolTable;
        $table->addSymbol(RangeSet::import([1, 2]));
        $actualValue = $table->addSymbol(RangeSet::import([3, 4]));
        self::assertGreaterThan(0, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 0 is not defined in symbol table
     */
    public function testReplaceSymbol_SymbolNotExists_ThrowsException(): void
    {
        (new SymbolTable)->replaceSymbol(0, RangeSet::import([1, 2]));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 0 is not defined in symbol table
     */
    public function testGetRangeSet_SymbolNotExists_ThrowsException(): void
    {
        (new SymbolTable)->getRangeSet(0);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testReplaceSymbol_SymbolAdded_GetRangeSetReturnsAddedRangeSet(): void
    {
        $table = new SymbolTable;
        $rangeSet = RangeSet::import([1, 2]);
        $symbolId = $table->addSymbol($rangeSet);
        $newRangeSet = RangeSet::import([3, 4]);
        $table->replaceSymbol($symbolId, $newRangeSet);
        $actualValue = $table->getRangeSet($symbolId);
        self::assertSame($actualValue, $newRangeSet);
    }

    public function testGetRangeSetList_NoSymbolAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new SymbolTable)->getRangeSetList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetRangeSetList_SymbolAdded_ReturnsMatchingList(): void
    {
        $table = new SymbolTable;
        $rangeSet = RangeSet::import([1, 2]);
        $table->addSymbol($rangeSet);
        $actualList = $table->getRangeSetList();
        self::assertArrayHasKey(0, $actualList);
        self::assertSame($rangeSet, $actualList[0]);
    }
}
