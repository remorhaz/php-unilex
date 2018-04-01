<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\LanguageMap;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\StateMapInterface;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\LanguageMap
 */
class LanguageMapTest extends TestCase
{

    public function testGetTransitionList_NoTransitionsAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new LanguageMap($this->createStateMap()))->getTranslationList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAddTransition_NoTransitionsAdded_GetTransitionListReturnsMatchingValue(): void
    {
        $languageMap = new LanguageMap($this->createStateMap());
        $languageMap->addTransition(1, 2, new Range(1, 2));
        $expectedValue = [1 => [2 => [0]]];
        $actualValue = $languageMap->getTranslationList();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param array $firstTransitionData
     * @param array $secondTransitionData
     * @param array $firstRangeData
     * @param array $secondRangeData
     * @param array $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAddTransitionCalledTwice
     */
    public function testAddTransition_TransitionWithSameRangeAdded_GetTransitionListReturnsMatchingValue(
        array $firstTransitionData,
        array $secondTransitionData,
        array $firstRangeData,
        array $secondRangeData,
        array $expectedValue
    ): void {
        $languageMap = new LanguageMap($this->createStateMap());
        [$stateIn, $stateOut] = $firstTransitionData;
        $languageMap->addTransition($stateIn, $stateOut, ...Range::importList(...$firstRangeData));
        [$stateIn, $stateOut] = $secondTransitionData;
        $languageMap->addTransition($stateIn, $stateOut, ...Range::importList(...$secondRangeData));
        $actualValue = $languageMap->getTranslationList();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerAddTransitionCalledTwice(): array
    {
        return [
            "Same ranges" => [[1, 2], [1, 3], [[1, 2]], [[1, 2]], [1 => [2 => [0], 3 => [0]]]],
            "Not intersecting ranges" => [[1, 2], [1, 3], [[1, 2]], [[3, 4]], [1 => [2 => [0], 3 => [1]]]],
            "Partially intersecting ranges" => [[1, 2], [1, 3], [[1, 2]], [[2, 4]], [1 => [2 => [0, 1], 3 => [1, 2]]]],
        ];
    }

    private function createStateMap(): StateMapInterface
    {
        return new class implements StateMapInterface {

            public function stateExists(int $stateId): bool
            {
                return true;
            }
        };
    }
}
