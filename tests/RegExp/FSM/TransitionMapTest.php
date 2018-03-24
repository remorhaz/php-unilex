<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\StateMap;
use Remorhaz\UniLex\RegExp\FSM\TransitionMap;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\TransitionMap
 */
class TransitionMapTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition start state: 0
     */
    public function testAddTransition_FromStateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        (new TransitionMap($stateMap))->addTransition(0, $stateId, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition finish state: 0
     */
    public function testAddTransition_ToStateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        (new TransitionMap($stateMap))->addTransition($stateId, 0, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition start state: 0
     */
    public function testTransitionExists_FromStateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $toStateId = $stateMap->createState();
        (new TransitionMap($stateMap))->transitionExists(0, $toStateId);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition finish state: 0
     */
    public function testTransitionExists_ToStateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        (new TransitionMap($stateMap))->transitionExists($fromStateId, 0);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testTransitionExists_TransitionAdded_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $transitionMap = new TransitionMap($stateMap);
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $transitionMap->addTransition($fromStateId, $toStateId, true);
        $actualValue = $transitionMap->transitionExists($fromStateId, $toStateId);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEpsilonTransitionExists_TransitionNotAdded_ReturnsFalse(): void
    {
        $stateMap = new StateMap;
        $transitionMap = new TransitionMap($stateMap);
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $actualValue = $transitionMap->transitionExists($fromStateId, $toStateId);
        self::assertFalse($actualValue);
    }


    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition start state: 0
     */
    public function testGetTransition_FromStateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        (new TransitionMap($stateMap))->addTransition(0, $stateId, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid transition finish state: 0
     */
    public function testGetTransition_ToStateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        (new TransitionMap($stateMap))->addTransition($stateId, 0, true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTransition_TransitionAdded_ReturnsMatchingData(): void
    {
        $stateMap = new StateMap;
        $transitionMap = new TransitionMap($stateMap);
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $transitionMap->addTransition($fromStateId, $toStateId, 1);
        $actualValue = $transitionMap->getTransition($fromStateId, $toStateId);
        self::assertSame(1, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Transition 1->2 is not defined
     */
    public function testGetTransition_TransitionNotAdded_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        (new TransitionMap($stateMap))->getTransition($fromStateId, $toStateId);
    }
}
