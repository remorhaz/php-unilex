<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\DfaBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\DfaBuilder
 */
class DfaBuilderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetStateEpsilonClosure_ValidNfa_ReturnsMatchingStateList(): void
    {
        $nfa = new Nfa;
        $stateList = [];
        for ($i = 0; $i < 10; $i++) {
            $stateList[$i] = $nfa->getStateMap()->createState();
        }
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[0], $stateList[1], true);
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[0], $stateList[7], true);
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[1], $stateList[2], true);
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[1], $stateList[4], true);
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[3], $stateList[6], true);
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[5], $stateList[6], true);
        $nfa->getEpsilonTransitionMap()->addTransition($stateList[6], $stateList[1], true);
        $dfaBuilder = new DfaBuilder($nfa);
        $actualValue = $dfaBuilder->getStateEpsilonClosure($stateList[0]);
        sort($actualValue);
        $expectedValue = [
            $stateList[0],
            $stateList[1],
            $stateList[2],
            $stateList[4],
            $stateList[7],
        ];
        self::assertSame($expectedValue, $actualValue);
    }
}
