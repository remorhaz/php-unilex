<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\Range;
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
        $stateMap->addRangeTransition($fromStateId, $toStateId, new Range(0x60, 0x63));
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
        $stateMap->addRangeTransition($fromStateId, $toStateId, new Range(0x62, 0x63));
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
        $rangeList = [
            new Range(0x59, 0x60),
            new Range(0x62, 0x63),
        ];
        $stateMap
            ->addRangeTransition($fromStateId, $toStateId, ...$rangeList);
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

    public function testGetCharTransitionList_RangeTransitionNotAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new StateMap)->getCharTransitionList();
        self::assertSame([], $actualValue);
    }

    public function testGetEpsilonTransitionList_EpsilonTransitionNotAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new StateMap)->getEpsilonTransitionList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetCharTransitionList_RangeTransitionAdded_ReturnsAddedTransition(): void
    {
        $stateMap = new StateMap;
        $stateIn = $stateMap->createState();
        $stateOut = $stateMap->createState();
        $stateMap->addRangeTransition($stateIn, $stateOut, new Range(0x61, 0x63));
        $expectedValue[$stateIn][$stateOut] = [[0x61, 0x63]];
        $actualValue = $this->exportRangeTransitionMap($stateMap->getCharTransitionList());
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetEpsilonTransitionList_EpsilonTransitionAdded_ReturnsAddedTransition(): void
    {
        $stateMap = new StateMap;
        $stateIn = $stateMap->createState();
        $stateOut = $stateMap->createState();
        $stateMap->addEpsilonTransition($stateIn, $stateOut);
        $expectedValue[$stateIn][$stateOut] = true;
        $actualValue = $stateMap->getEpsilonTransitionList();
        self::assertSame($expectedValue, $actualValue);
    }

    private function exportRangeTransitionMap(array $transitionMap): array
    {
        $rangeDataList = [];
        foreach ($transitionMap as $stateIn => $stateOutMap) {
            /** @var Range[] $rangeList */
            foreach ($stateOutMap as $stateOut => $rangeList) {
                foreach ($rangeList as $range) {
                    $rangeDataList[$stateIn][$stateOut][] = [$range->getStart(), $range->getFinish()];
                }
            }
        }
        return $rangeDataList;
    }
}
