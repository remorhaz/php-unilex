<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\FSM\SymbolTable;

class SymbolTableTest extends TestCase
{

    /**
     * @throws UniLexException
     */
    public function testAddSymbol_NoSymbolAdded_ReturnsZero(): void
    {
        $actualValue = (new SymbolTable)->addSymbol(RangeSet::import([1, 2]));
        self::assertSame(0, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testAddSymbol_SymbolAdded_ReturnsValueGreaterThanZero(): void
    {
        $table = new SymbolTable;
        $table->addSymbol(RangeSet::import([1, 2]));
        $actualValue = $table->addSymbol(RangeSet::import([3, 4]));
        self::assertGreaterThan(0, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testReplaceSymbol_SymbolNotExists_ThrowsException(): void
    {
        $symbolTable = new SymbolTable;

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 0 is not defined in symbol table');
        $symbolTable->replaceSymbol(0, RangeSet::import([1, 2]));
    }

    /**
     * @throws UniLexException
     */
    public function testGetRangeSet_SymbolNotExists_ThrowsException(): void
    {
        $symbolTable = new SymbolTable;

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 0 is not defined in symbol table');
        $symbolTable->getRangeSet(0);
    }

    /**
     * @throws UniLexException
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
     * @throws UniLexException
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
