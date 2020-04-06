<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\NfaCalc;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\NfaCalc
 */
class NfaCalcTest extends TestCase
{

    /**
     * @throws UniLexException
     */
    public function testGetEpsilonClosure_ValidNfa_ReturnsMatchingStateList(): void
    {
        $nfaCalc = new NfaCalc($this->createNfa());
        $actualValue = $nfaCalc->getEpsilonClosure(0);
        $expectedValue = [0, 1, 2, 4, 7];
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetSymbolMoves_ValidNfa_ReturnsMatchingStateList(): void
    {
        $nfaCalc = new NfaCalc($this->createNfa());
        $actualValue = $nfaCalc->getSymbolMoves(0, ...$nfaCalc->getEpsilonClosure(0));
        self::assertEquals([3, 8], $actualValue);
    }

    /**
     * @return Nfa
     * @throws UniLexException
     */
    private function createNfa(): Nfa
    {
        $nfa = new Nfa();
        $stateList = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $nfa->getStateMap()->importState(true, ...$stateList);
        $nfa->getStateMap()->addStartState(0);
        $nfa->getStateMap()->addFinishState(10);
        $epsilonTransitionList = [[0, 1], [0, 7], [1, 2], [1, 4], [3, 6], [5, 6], [6, 1], [6, 7]];
        foreach ($epsilonTransitionList as $epsilonTransition) {
            [$stateIn, $stateOut] = $epsilonTransition;
            $nfa
                ->getEpsilonTransitionMap()
                ->addTransition($stateIn, $stateOut, true);
        }
        $rangeList = [0 => [[0x61, 0x61]], 1 => [[0x62, 0x62]]];
        foreach ($rangeList as $symbolId => $rangeSetData) {
            $nfa
                ->getSymbolTable()
                ->importSymbol($symbolId, RangeSet::import(...$rangeSetData));
        }
        $symbolTransitionList = [[2, 3, [0]], [4, 5, [1]], [7, 8, [0]], [8, 9, [1]], [9, 10, [1]]];
        foreach ($symbolTransitionList as $symbolTransition) {
            [$stateIn, $stateOut, $symbolList] = $symbolTransition;
            $nfa
                ->getSymbolTransitionMap()
                ->addTransition($stateIn, $stateOut, $symbolList);
        }
        return $nfa;
    }
}
