<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\StateMap;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\StateMap
 */
class StateMapTest extends TestCase
{

    public function testCreateState_Always_ReturnsPositiveInteger(): void
    {
        $actualValue = (new StateMap)->createState();
        self::assertGreaterThan(0, $actualValue);
    }

    public function testCreateState_CalledTwice_ReturnsDifferentValues(): void
    {
        $stateMap = new StateMap;
        $firstStateId = $stateMap->createState();
        $secondStateId = $stateMap->createState();
        self::assertNotEquals($firstStateId, $secondStateId);
    }

    public function testStateExists_StateNotCreated_ReturnsFalse(): void
    {
        $actualValue = (new StateMap)->stateExists(1);
        self::assertFalse($actualValue);
    }

    public function testStateExists_StateCreated_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        $actualValue = $stateMap->stateExists($stateId);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage State 1 is undefined
     */
    public function testSetStartState_StateNotExists_ThrowsException(): void
    {
        (new StateMap)->setStartState(1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Start state is already set
     */
    public function testSetStartState_StartStateIsSet_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateMap->setStartState($stateMap->createState());
        $stateMap->setStartState($stateMap->createState());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Start state is undefined
     */
    public function testGetStartState_StartStateIsNotSet_ThrowsException(): void
    {
        (new StateMap)->getStartState();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetStartState_StartStateIsSet_ReturnsStartState(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        $stateMap->setStartState($stateId);
        $actualValue = $stateMap->getStartState();
        self::assertSame($stateId, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEpsilonTransitionExists_EpsilonTransitionAdded_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $stateMap->addEpsilonTransition($fromStateId, $toStateId);
        $actualValue = $stateMap->epsilonTransitionExists($fromStateId, $toStateId);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEpsilonTransitionExists_EpsilonTransitionNotAdded_ReturnsFalse(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $actualValue = $stateMap->epsilonTransitionExists($fromStateId, $toStateId);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCharTransitionExists_MatchingCharTransitionAdded_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $stateMap->addCharTransition($fromStateId, $toStateId, 0x61);
        $actualValue = $stateMap->charTransitionExists($fromStateId, $toStateId, 0x61);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCharTransitionExists_MatchingRangeTransitionAdded_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $stateMap->addRangeTransition($fromStateId, $toStateId, 0x60, 0x63);
        $actualValue = $stateMap->charTransitionExists($fromStateId, $toStateId, 0x61);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCharTransitionExists_NotMatchingRangeTransitionAdded_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $stateMap->addRangeTransition($fromStateId, $toStateId, 0x62, 0x63);
        $actualValue = $stateMap->charTransitionExists($fromStateId, $toStateId, 0x61);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCharTransitionExists_TwoNotMatchingRangeTransitionsAdded_ReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $stateMap->addRangeTransition($fromStateId, $toStateId, 0x59, 0x60);
        $stateMap->addRangeTransition($fromStateId, $toStateId, 0x62, 0x63);
        $actualValue = $stateMap->charTransitionExists($fromStateId, $toStateId, 0x61);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCharTransitionExists_CharTransitionNotAdded_ReturnsFalse(): void
    {
        $stateMap = new StateMap;
        $fromStateId = $stateMap->createState();
        $toStateId = $stateMap->createState();
        $actualValue = $stateMap->charTransitionExists($fromStateId, $toStateId, 0x61);
        self::assertFalse($actualValue);
    }
}
