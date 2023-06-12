<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\IntRangeSets\RangeInterface;
use Remorhaz\IntRangeSets\RangeSetInterface;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\RegExp\FSM\LanguageBuilder;
use Remorhaz\UniLex\RegExp\FSM\Nfa;
use Remorhaz\IntRangeSets\Range;
use Remorhaz\UniLex\RegExp\FSM\StateMapInterface;
use Remorhaz\UniLex\RegExp\FSM\SymbolTable;
use Remorhaz\UniLex\RegExp\FSM\TransitionMap;

use function array_map;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\LanguageBuilder
 */
class LanguageBuilderTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testFromNfa_EmptyNfa_AddTransitionAddsSymbolToNfaSymbolTable(): void
    {
        $nfa = new Nfa();
        $stateIn = $nfa->getStateMap()->createState();
        $stateOut = $nfa->getStateMap()->createState();
        LanguageBuilder::forNfa($nfa)->addTransition($stateIn, $stateOut, new Range(1, 2));
        $actualRangeSetList = $nfa->getSymbolTable()->getRangeSetList();
        self::assertArrayHasKey(0, $actualRangeSetList);
        self::assertEquals([[1, 2]], $this->exportRangeSet($actualRangeSetList[0]));
    }

    /**
     * @throws UniLexException
     */
    public function testFromNfa_EmptyNfa_AddTransitionAddsTransitionToNfaSymbolTransitionMap(): void
    {
        $nfa = new Nfa();
        $stateIn = $nfa->getStateMap()->createState();
        $stateOut = $nfa->getStateMap()->createState();
        LanguageBuilder::forNfa($nfa)->addTransition($stateIn, $stateOut, new Range(1, 2));
        $expectedTransitionList = [$stateIn => [$stateOut => [0]]];
        $actualTransitionList = $nfa->getSymbolTransitionMap()->getTransitionList();
        self::assertEquals($expectedTransitionList, $actualTransitionList);
    }

    /**
     * @throws UniLexException
     */
    public function testAddTransition_NoTransitionsAdded_TransitionMapContainsMatchingList(): void
    {
        $symbolTable = new SymbolTable();
        $transitionMap = new TransitionMap($this->createStateMap());
        $languageBuilder = new LanguageBuilder($symbolTable, $transitionMap);
        $languageBuilder->addTransition(1, 2, new Range(1, 2));
        $expectedValue = [1 => [2 => [0]]];
        $actualValue = $transitionMap->getTransitionList();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @param array $firstTransitionData
     * @param array $secondTransitionData
     * @param array $expectedValue
     * @throws UniLexException
     * @dataProvider providerAddTransitionCalledTwiceTransitions
     */
    public function testAddTransition_TransitionWithSameRangeAdded_TransitionMapContainsMatchingList(
        array $firstTransitionData,
        array $secondTransitionData,
        array $expectedValue
    ): void {
        $symbolTable = new SymbolTable();
        $transitionMap = new TransitionMap($this->createStateMap());
        $languageBuilder = new LanguageBuilder($symbolTable, $transitionMap);
        [$stateIn, $stateOut, $firstRangeData] = $firstTransitionData;
        $languageBuilder->addTransition($stateIn, $stateOut, ...$this->importRanges(...$firstRangeData));
        [$stateIn, $stateOut, $secondRangeData] = $secondTransitionData;
        $languageBuilder->addTransition($stateIn, $stateOut, ...$this->importRanges(...$secondRangeData));
        $actualValue = $transitionMap->getTransitionList();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{array, array, array}>
     */
    public static function providerAddTransitionCalledTwiceTransitions(): iterable
    {
        return [
            "Same ranges" => [
                [1, 2, [[1, 2]]],
                [1, 3, [[1, 2]]],
                [1 => [2 => [0], 3 => [0]]],
            ],
            "Not intersecting ranges" => [
                [1, 2, [[1, 2]]],
                [1, 3, [[3, 4]]],
                [1 => [2 => [0], 3 => [1]]],
            ],
            "Partially intersecting ranges" => [
                [1, 2, [[1, 2]]],
                [1, 3, [[2, 4]]],
                [1 => [2 => [0, 1], 3 => [1, 2]]],
            ],
        ];
    }

    /**
     * @param array $firstTransitionData
     * @param array $secondTransitionData
     * @param array $firstRangeData
     * @param array $secondRangeData
     * @param array $expectedValue
     * @throws UniLexException
     * @dataProvider providerAddTransitionCalledTwiceSymbols
     */
    public function testAddTransition_TransitionWithSameRangeAdded_GetSymbolMapReturnsMatchingValue(
        array $firstTransitionData,
        array $secondTransitionData,
        array $firstRangeData,
        array $secondRangeData,
        array $expectedValue
    ): void {
        $symbolTable = new SymbolTable();
        $transitionMap = new TransitionMap($this->createStateMap());
        $languageBuilder = new LanguageBuilder($symbolTable, $transitionMap);
        [$stateIn, $stateOut] = $firstTransitionData;
        $languageBuilder->addTransition(
            $stateIn,
            $stateOut,
            ...array_map([$this, 'importRange'], $firstRangeData)
        );
        [$stateIn, $stateOut] = $secondTransitionData;
        $languageBuilder->addTransition(
            $stateIn,
            $stateOut,
            ...array_map([$this, 'importRange'], $secondRangeData)
        );
        $actualValue = $this->exportSymbolMap($symbolTable->getRangeSetList());
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @return iterable<string, array{array, array, array, array, array}>
     */
    public static function providerAddTransitionCalledTwiceSymbols(): iterable
    {
        return [
            "Same ranges" => [[1, 2], [1, 3], [[1, 2]], [[1, 2]], [0 => [[1, 2]]]],
            "Not intersecting ranges" => [[1, 2], [1, 3], [[1, 2]], [[3, 4]], [0 => [[1, 2]], 1 => [[3, 4]]]],
            "Partially intersecting ranges" => [
                [1, 2],
                [1, 3],
                [[1, 2]],
                [[2, 4]],
                [0 => [[1, 1]], 1 => [[2, 2]], 2 => [[3, 4]]],
            ],
            "Second range in the middle of first" => [
                [1, 2],
                [1, 3],
                [[1, 4]],
                [[2, 3]],
                [0 => [[1, 1], [4, 4]], 1 => [[2, 3]]],
            ],
            "First range in the middle of second" => [
                [1, 2],
                [1, 3],
                [[2, 3]],
                [[1, 4]],
                [0 => [[2, 3]], 1 => [[1, 1], [4, 4]]],
            ],
        ];
    }

    private function createStateMap(): StateMapInterface
    {
        return new class implements StateMapInterface
        {
            public function stateExists(int $stateId): bool
            {
                return true;
            }
        };
    }

    /**
     * @param RangeSetInterface[] $symbolMap
     * @return array
     */
    private function exportSymbolMap(array $symbolMap): array
    {
        $result = [];
        foreach ($symbolMap as $symbolId => $rangeSet) {
            $result[$symbolId] = array_map(
                function (RangeInterface $range): array {
                    return [$range->getStart(), $range->getFinish()];
                },
                $rangeSet->getRanges()
            );
        }

        return $result;
    }

    private function importRange(array $rangeData): RangeInterface
    {
        return new Range(...$rangeData);
    }

    private function importRanges(array ...$rangeDataList): array
    {
        return array_map([$this, 'importRange'], $rangeDataList);
    }

    private function exportRange(RangeInterface $range): array
    {
        return [$range->getStart(), $range->getFinish()];
    }

    private function exportRangeSet(RangeSetInterface $rangeSet): array
    {
        return array_map([$this, 'exportRange'], $rangeSet->getRanges());
    }
}
