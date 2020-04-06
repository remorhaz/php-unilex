<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\StateMap;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\StateMap
 */
class StateMapTest extends TestCase
{

    /**
     * @throws UniLexException
     */
    public function testCreateState_Always_ReturnsPositiveInteger(): void
    {
        $actualValue = (new StateMap())->createState();
        self::assertGreaterThan(0, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testCreateState_CalledTwice_ReturnsDifferentValues(): void
    {
        $stateMap = new StateMap();
        $firstStateId = $stateMap->createState();
        $secondStateId = $stateMap->createState();
        self::assertNotEquals($firstStateId, $secondStateId);
    }

    /**
     * @throws UniLexException
     */
    public function testCreateState_NullValue_ThrowsException(): void
    {
        $stateMap = new StateMap();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Null state value is not allowed');
        $stateMap->createState(null);
    }

    /**
     * @throws UniLexException
     */
    public function testGetValueState_CustomStateCreated_ReturnsCreatedStateId(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState(3);
        $actualValue = $stateMap->getValueState(3);
        self::assertSame($stateId, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetValueState_NoStatesCreated_ThrowsException(): void
    {
        $stateMap = new StateMap();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Value not found in state map');
        $stateMap->getValueState(true);
    }

    /**
     * @throws UniLexException
     */
    public function testGetValueState_NoStateWithValueExists_ThrowsException(): void
    {
        $stateMap = new StateMap();
        $stateMap->createState(1);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Value not found in state map');
        $stateMap->getValueState(2);
    }

    /**
     * @throws UniLexException
     */
    public function testStateValueExists_StateWithValueCreated_ReturnsTrue(): void
    {
        $stateMap = new StateMap();
        $stateMap->createState(1);
        $actualValue = $stateMap->stateValueExists(1);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
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
        $actualValue = (new StateMap())->stateExists(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testStateExists_StateCreated_ReturnsTrue(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState();
        $actualValue = $stateMap->stateExists($stateId);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testAddStartState_StateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('State 1 is undefined');
        $stateMap->addStartState(1);
    }

    /**
     * @throws UniLexException
     */
    public function testAddStartState_SameStateIsSet_ThrowsException(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState();
        $stateMap->addStartState($stateId);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage("Start state {$stateId} is already set");
        $stateMap->addStartState($stateId);
    }

    /**
     * @throws UniLexException
     */
    public function testGetStartState_StartStateIsNotSet_ThrowsException(): void
    {
        $stateMap = new StateMap();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Start state is undefined');
        $stateMap->getStartState();
    }

    /**
     * @throws UniLexException
     */
    public function testGetStartState_StartStateIsSet_ReturnsStartState(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState();
        $stateMap->addStartState($stateId);
        $actualValue = $stateMap->getStartState();
        self::assertSame($stateId, $actualValue);
    }

    public function testGetStateList_NoStatesCreated_ReturnsEmptyArray(): void
    {
        $actualValue = (new StateMap())->getStateList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetStateList_TwoStatesImported_ReturnsImportedStates(): void
    {
        $stateMap = new StateMap();
        $stateMap->importState(true, 1, 2);
        $actualValue = $stateMap->getStateList();
        self::assertEquals([1, 2], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testImportState_StateNotExists_StateExistsReturnsTrue(): void
    {
        $stateMap = new StateMap();
        $stateMap->importState(true, 1);
        $actualValue = $stateMap->stateExists(1);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testImportState_StateCreated_ThrowsException(): void
    {
        $stateMap = new StateMap();
        $stateMap->createState();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('State 1 already exists');
        $stateMap->importState(true, 1);
    }

    /**
     * @throws UniLexException
     */
    public function testImportState_StateImported_ThrowsException(): void
    {
        $stateMap = new StateMap();
        $stateMap->importState(true, 1);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('State 1 already exists');
        $stateMap->importState(true, 1);
    }

    /**
     * @throws UniLexException
     */
    public function testImportState_ValidState_CreateStateReturnsValuePlusOne(): void
    {
        $stateMap = new StateMap();
        $stateMap->importState(true, 5);
        $actualValue = $stateMap->createState();
        self::assertSame(6, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testAddFinishState_StateNotExists_ThrowsException()
    {
        $stateMap = new StateMap();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('State 1 is undefined');
        $stateMap->addFinishState(1);
    }

    /**
     * @throws UniLexException
     */
    public function testAddFinishState_StateExists_IsFinishStateReturnsTrue(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState();
        $stateMap->addFinishState($stateId);
        $actualValue = $stateMap->isFinishState($stateId);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testAddFinishState_StateIsFinish_ThrowsException(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState();
        $stateMap->addFinishState($stateId);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Finish state 1 is already set');
        $stateMap->addFinishState($stateId);
    }

    /**
     * @throws UniLexException
     */
    public function testIsFinishState_StateNotExists_ThrowsException(): void
    {
        $stateMap = new StateMap();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('State 1 is undefined');
        $stateMap->isFinishState(1);
    }

    /**
     * @throws UniLexException
     */
    public function testIsFinishState_StateIsNotFinish_ReturnsFalse(): void
    {
        $stateMap = new StateMap();
        $stateId = $stateMap->createState();
        $actualValue = $stateMap->isFinishState($stateId);
        self::assertFalse($actualValue);
    }

    public function testGetFinishStateList_NoFinishStates_ReturnsEmptyArray(): void
    {
        $actualValue = (new StateMap())->getFinishStateList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetFinishStateList_TwoFinishStatesAdded_ReturnsMatchingStates(): void
    {
        $stateMap = new StateMap();
        $stateMap->importState(true, 1, 2, 3);
        $stateMap->addFinishState(2, 3);
        $actualValue = $stateMap->getFinishStateList();
        self::assertEquals([2, 3], $actualValue);
    }
}
