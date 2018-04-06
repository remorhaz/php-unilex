<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\Dfa;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\DfaBuilder
 */
class DfaBuilderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRun_ValidNfa_ReturnsMatchingDfa(): void
    {
        $dfa = new Dfa;
        $nfaBuilder = new DfaBuilder($dfa, $this->createNfa());
        $nfaBuilder->run();
        $rangeSetList = [];
        foreach ($dfa->getSymbolTable()->getRangeSetList() as $symbolId => $rangeSet) {
            $rangeSetList[$symbolId] = $rangeSet->export();
        }
        self::assertEquals([0 => [[0x61, 0x61]], 1 => [[0x62, 0x62]]], $rangeSetList);
        self::assertEquals([1, 2, 3, 4, 5], $dfa->getStateMap()->getStateList());
        $transitionList = $dfa->getTransitionMap()->getTransitionList();
        $expectedTransitionList = [
            1 => [2 => [0], 3 => [1]],
            2 => [2 => [0], 4 => [1]],
            3 => [2 => [0], 3 => [1]],
            4 => [2 => [0], 5 => [1]],
            5 => [2 => [0], 3 => [1]],
        ];
        self::assertEquals($expectedTransitionList, $transitionList);
    }

    /**
     * @return Nfa
     * @throws \Remorhaz\UniLex\Exception
     */
    private function createNfa(): Nfa
    {
        $nfa = new Nfa;
        $stateList = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $nfa->getStateMap()->importState(...$stateList);
        $nfa->getStateMap()->setStartState(0);
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
