<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\Nfa;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\Nfa
 */
class NfaTest extends TestCase
{
    public function testGetStateMap_CalledTwice_ReturnsSameInstance(): void
    {
        $nfa = new Nfa();
        $stateMap = $nfa->getStateMap();
        $anotherStateMap = $nfa->getStateMap();
        self::assertSame($stateMap, $anotherStateMap);
    }

    public function testGetEpsilonTransitionMap_CalledTwice_ReturnsSameInstance(): void
    {
        $nfa = new Nfa();
        $transitionMap = $nfa->getEpsilonTransitionMap();
        $anotherTransitionMap = $nfa->getEpsilonTransitionMap();
        self::assertSame($transitionMap, $anotherTransitionMap);
    }

    public function testGetSymbolTransitionMap_CalledTwice_ReturnsSameInstance(): void
    {
        $nfa = new Nfa();
        $transitionMap = $nfa->getSymbolTransitionMap();
        $anotherTransitionMap = $nfa->getSymbolTransitionMap();
        self::assertSame($transitionMap, $anotherTransitionMap);
    }

    public function testGetSymbolTableMap_CalledTwice_ReturnsSameInstance(): void
    {
        $nfa = new Nfa();
        $table = $nfa->getSymbolTable();
        $anotherTable = $nfa->getSymbolTable();
        self::assertSame($table, $anotherTable);
    }
}
