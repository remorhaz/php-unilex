<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\SymbolTable;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\Dfa
 */
class DfaTest extends TestCase
{

    public function testGetStateMap_CalledTwice_ReturnsSameInstance(): void
    {
        $dfa = new Dfa;
        $stateMap = $dfa->getStateMap();
        $anotherStateMap = $dfa->getStateMap();
        self::assertSame($stateMap, $anotherStateMap);
    }

    public function testGetTransitionMap_CalledTwice_ReturnsSameInstance(): void
    {
        $dfa = new Dfa;
        $transitionMap = $dfa->getTransitionMap();
        $anotherTransitionMap = $dfa->getTransitionMap();
        self::assertSame($transitionMap, $anotherTransitionMap);
    }

    public function testGetSymbolTable_CalledTwice_ReturnsSameInstance(): void
    {
        $dfa = new Dfa;
        $symbolTable = $dfa->getSymbolTable();
        $anotherSymbolTable = $dfa->getSymbolTable();
        self::assertSame($symbolTable, $anotherSymbolTable);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol table already exists in DFA
     */
    public function testSetSymbolTable_CalledAfterGetSymbolTable_ThrowsException(): void
    {
        $dfa = new Dfa;
        $dfa->getSymbolTable();
        $dfa->setSymbolTable(new SymbolTable);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbolTable_SetSymbolTableCalled_ReturnsSameInstance(): void
    {
        $dfa = new Dfa;
        $symbolTable = new SymbolTable;
        $dfa->setSymbolTable($symbolTable);
        $actualValue = $dfa->getSymbolTable();
        self::assertSame($symbolTable, $actualValue);
    }
}
