<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\RangeSet
 */
class RangeSetTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetRanges_ConstructedWithoutArguments_ReturnsEmptyArray(): void
    {
        $actualValue = (new RangeSet)->getRanges();
        self::assertEquals([], $actualValue);
    }

    /**
     * @param array $ranges
     * @param array $expectedRanges
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAddableRanges
     */
    public function testGetRanges_ConstructWithRanges_ReturnsMergedRanges(array $ranges, array $expectedRanges): void
    {
        $rangeList = (new RangeSet(...$this->importRangeList(...$ranges)))->getRanges();
        $actualValue = $this->exportRangeList(...$rangeList);
        self::assertEquals($expectedRanges, $actualValue);
    }

    /**
     * @param array $ranges
     * @param array $expectedRanges
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAddableRanges
     */
    public function testAddRange_ValidRanges_GetRangesReturnsMergedRanges(array $ranges, array $expectedRanges): void
    {
        $rangeSet = new RangeSet;
        $rangeSet->addRange(...$this->importRangeList(...$ranges));
        $actualValue = $this->exportRangeList(...$rangeSet->getRanges());
        self::assertEquals($expectedRanges, $actualValue);
    }

    public function providerAddableRanges(): array
    {
        return [
            "Empty array" => [[], []],
            "Single range" => [[[1, 2]], [[1, 2]]],
            "Two equal single ranges" => [[[1, 2], [1, 2]], [[1, 2]]],
            "Two not neighbour ranges" => [[[1, 2], [4, 5]], [[1, 2], [4, 5]]],
            "Two neighbour ranges" => [[[1, 2], [3, 4]], [[1, 4]]],
            "Two intersecting ranges" => [[[1, 3], [2, 4]], [[1, 4]]],
        ];
    }

    /**
     * @param array $firstRanges
     * @param array $secondRanges
     * @param array $expectedRanges
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerDoubleAddableRanges
     */
    public function testAddRange_CalledTwice_GetRangesReturnsMergedRanges(
        array $firstRanges,
        array $secondRanges,
        array $expectedRanges
    ) {
        $rangeSet = new RangeSet;
        $rangeSet->addRange(...$this->importRangeList(...$firstRanges));
        $rangeSet->addRange(...$this->importRangeList(...$secondRanges));
        $actualValue = $this->exportRangeList(...$rangeSet->getRanges());
        self::assertEquals($expectedRanges, $actualValue);
    }

    public function providerDoubleAddableRanges(): array
    {
        return [
            "Empty array after single range" => [[[1, 2]], [], [[1, 2]]],
            "Same single range" => [[[1, 2]], [[1, 2]], [[1, 2]]],
            "Two not neighbour ranges" => [[[1, 2]], [[4, 5]], [[1, 2], [4, 5]]],
            "Two intersecting ranges" => [[[1, 3]], [[2, 4]], [[1, 4]]],
        ];
    }

    /**
     * @param array $firstRanges
     * @param array $secondRanges
     * @param array $expectedDiff
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerDiffRanges
     */
    public function testGetDiffRange_ValidRangeLists_ReturnsDiffRangeList(
        array $firstRanges,
        array $secondRanges,
        array $expectedDiff
    ): void {
        $diffRanges = (new RangeSet(...$this->importRangeList(...$firstRanges)))
            ->getDiff(...$this->importRangeList(...$secondRanges))
            ->getRanges();
        $actualValue = $this->exportRangeList(...$diffRanges);
        self::assertEquals($expectedDiff, $actualValue);
    }

    public function providerDiffRanges(): array
    {
        return [
            "Empty range" => [[[1, 2]], [], [[1, 2]]],
            "Empty existing range" => [[], [[1, 2]], [[1, 2]]],
            "Range after existing range" => [[[1, 2]], [[4, 5]], [[1, 2], [4, 5]]],
            "Range before existing range" => [[[4, 5]], [[1, 2]], [[1, 2], [4, 5]]],
            "Range right before existing range" => [[[2, 5]], [[1, 1]], [[1, 5]]],
            "Range partially before existing range" => [[[2, 5]], [[1, 3]], [[1, 1], [4, 5]]],
            "Range entirely inside existing range" => [[[2, 5]], [[3, 3]], [[2, 2], [4, 5]]],
            "Range starts before existing range with matching ends" => [[[2, 5]], [[1, 5]], [[1, 1]]],
            "Range starts before and ends after existing range" => [[[2, 5]], [[3, 3]], [[2, 2], [4, 5]]],
            "Range starts before and ends after all existing ranges" =>
                [[[2, 5], [7, 10]], [[1, 13]], [[1, 1], [6, 6], [11, 13]]],
            "Range partially intersects with two existing ranges" =>
                [[[2, 5], [7, 10]], [[3, 7]], [[2, 2], [6, 6], [8, 10]]],
        ];
    }

    private function importRangeList(array ...$rangeDataList): array
    {
        $rangeList = [];
        foreach ($rangeDataList as $rangeData) {
            $rangeList[] = new Range(...$rangeData);
        }
        return $rangeList;
    }

    private function exportRangeList(Range ...$rangeList): array
    {
        $rangeDataList = [];
        foreach ($rangeList as $range) {
            $rangeDataList[] = [$range->getFrom(), $range->getTo()];
        }
        return $rangeDataList;
    }
}
