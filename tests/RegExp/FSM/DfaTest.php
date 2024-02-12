<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\SymbolTable;

#[CoversClass(Dfa::class)]
class DfaTest extends TestCase
{
    public function testGetStateMap_CalledTwice_ReturnsSameInstance(): void
    {
        $dfa = new Dfa();
        $stateMap = $dfa->getStateMap();
        $anotherStateMap = $dfa->getStateMap();
        self::assertSame($stateMap, $anotherStateMap);
    }

    public function testGetTransitionMap_CalledTwice_ReturnsSameInstance(): void
    {
        $dfa = new Dfa();
        $transitionMap = $dfa->getTransitionMap();
        $anotherTransitionMap = $dfa->getTransitionMap();
        self::assertSame($transitionMap, $anotherTransitionMap);
    }

    public function testGetSymbolTable_CalledTwice_ReturnsSameInstance(): void
    {
        $dfa = new Dfa();
        $symbolTable = $dfa->getSymbolTable();
        $anotherSymbolTable = $dfa->getSymbolTable();
        self::assertSame($symbolTable, $anotherSymbolTable);
    }

    /**
     * @throws UniLexException
     */
    public function testSetSymbolTable_CalledAfterGetSymbolTable_ThrowsException(): void
    {
        $dfa = new Dfa();
        $dfa->getSymbolTable();
        $newSymbolTable = new SymbolTable();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol table already exists in DFA');
        $dfa->setSymbolTable($newSymbolTable);
    }

    /**
     * @throws UniLexException
     */
    public function testGetSymbolTable_SetSymbolTableCalled_ReturnsSameInstance(): void
    {
        $dfa = new Dfa();
        $symbolTable = new SymbolTable();
        $dfa->setSymbolTable($symbolTable);
        $actualValue = $dfa->getSymbolTable();
        self::assertSame($symbolTable, $actualValue);
    }
}
