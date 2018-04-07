<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\StateMap;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\StateMap
 */
class StateMapTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCreateState_Always_ReturnsPositiveInteger(): void
    {
        $actualValue = (new StateMap)->createState();
        self::assertGreaterThan(0, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCreateState_CalledTwice_ReturnsDifferentValues(): void
    {
        $stateMap = new StateMap;
        $firstStateId = $stateMap->createState();
        $secondStateId = $stateMap->createState();
        self::assertNotEquals($firstStateId, $secondStateId);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Null state value is not allowed
     */
    public function testCreateState_NullValue_ThrowsException(): void
    {
        (new StateMap)->createState(null);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetValueState_CustomStateCreated_ReturnsCreatedStateId(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState(3);
        $actualValue = $stateMap->getValueState(3);
        self::assertSame($stateId, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Value not found in state map
     */
    public function testGetValueState_NoStatesCreated_ThrowsException(): void
    {
        (new StateMap)->getValueState(true);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Value not found in state map
     */
    public function testGetValueState_NoStateWithValueExists_ThrowsException(): void
    {
        $stateMap = new StateMap();
        $stateMap->createState(1);
        $stateMap->getValueState(2);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStateValueExists_StateWithValueCreated_ReturnsTrue(): void
    {
        $stateMap = new StateMap();
        $stateMap->createState(1);
        $actualValue = $stateMap->stateValueExists(1);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStateValueExists_StateWithAnotherValueCreated_ReturnsFalse(): void
    {
        $stateMap = new StateMap();
        $stateMap->createState(1);
        $actualValue = $stateMap->stateValueExists(2);
        self::assertFalse($actualValue);
    }

    public function testStateExists_StateNotCreated_ReturnsFalse(): void
    {
        $actualValue = (new StateMap)->stateExists(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
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

    public function testGetStateList_NoStatesCreated_ReturnsEmptyArray(): void
    {
        $actualValue = (new StateMap)->getStateList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetStateList_TwoStatesImported_ReturnsImportedStates(): void
    {
        $stateMap = new StateMap;
        $stateMap->importState(true, 1, 2);
        $actualValue = $stateMap->getStateList();
        self::assertEquals([1, 2], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testImportState_StateNotExists_StateExistsReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $stateMap->importState(true, 1);
        $actualValue = $stateMap->stateExists(1);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage State 1 already exists
     */
    public function testImportState_StateCreated_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateMap->createState();
        $stateMap->importState(true, 1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage State 1 already exists
     */
    public function testImportState_StateImported_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateMap->createState();
        $stateMap->importState(true, 1);
        $stateMap->importState(true, 1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testImportState_ValidState_CreateStateReturnsValuePlusOne(): void
    {
        $stateMap = new StateMap;
        $stateMap->importState(true, 5);
        $actualValue = $stateMap->createState();
        self::assertSame(6, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage State 1 is undefined
     */
    public function testAddFinishState_StateNotExists_ThrowsException()
    {
        (new StateMap)->addFinishState(1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddFinishState_StateExists_IsFinishStateReturnsTrue(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        $stateMap->addFinishState($stateId);
        $actualValue = $stateMap->isFinishState($stateId);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Finish state 1 is already set
     */
    public function testAddFinishState_StateIsFinish_ThrowsException(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        $stateMap->addFinishState($stateId);
        $stateMap->addFinishState($stateId);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage State 1 is undefined
     */
    public function testIsFinishState_StateNotExists_ThrowsException(): void
    {
        (new StateMap)->isFinishState(1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testIsFinishState_StateIsNotFinish_ReturnsFalse(): void
    {
        $stateMap = new StateMap;
        $stateId = $stateMap->createState();
        $actualValue = $stateMap->isFinishState($stateId);
        self::assertFalse($actualValue);
    }

    public function testGetFinishStateList_NoFinishStates_ReturnsEmptyArray(): void
    {
        $actualValue = (new StateMap)->getFinishStateList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetFinishStateList_TwoFinishStatesAdded_ReturnsMatchingStates(): void
    {
        $stateMap = new StateMap;
        $stateMap->importState(true, 1, 2, 3);
        $stateMap->addFinishState(2, 3);
        $actualValue = $stateMap->getFinishStateList();
        self::assertEquals([2, 3], $actualValue);
    }
}
