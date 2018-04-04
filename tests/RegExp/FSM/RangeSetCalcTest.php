<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;
use Remorhaz\UniLex\RegExp\FSM\RangeSetCalc;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\RangeSetCalc
 */
class RangeSetCalcTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEquals_EmptyRangeSets_ReturnsTrue(): void
    {
        $actualValue = (new RangeSetCalc)->equals(new RangeSet, new RangeSet);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEquals_SameRangeSets_ReturnsTrue(): void
    {
        $rangeSet = RangeSet::import([1, 2], [4, 4]);
        $anotherRangeSet = RangeSet::import([1, 2], [4, 4]);
        $actualValue = (new RangeSetCalc)->equals($rangeSet, $anotherRangeSet);
        self::assertTrue($actualValue);
    }

    /**
     * @param array $rangeSetData
     * @param array $anotherRangeSetData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerDifferentRangeSets
     */
    public function testEquals_DifferentRangeSets_ReturnsFalse(
        array $rangeSetData,
        array $anotherRangeSetData
    ): void {
        $rangeSet = RangeSet::import(...$rangeSetData);
        $anotherRangeSet = RangeSet::import(...$anotherRangeSetData);
        $actualValue = (new RangeSetCalc)->equals($rangeSet, $anotherRangeSet);
        self::assertFalse($actualValue);
    }

    public function providerDifferentRangeSets(): array
    {
        return [
            "Count differs" => [[[1, 2], [4, 4]], [[1, 2]]],
            "Start differs" => [[[1, 2], [4, 4]], [[2, 2], [4, 4]]],
            "Finish differs" => [[[1, 2], [4, 4]], [[1, 2], [4, 5]]],
        ];
    }

    /**
     * @param array $rangeSet
     * @param array $anotherRangeSet
     * @param array $expectedRangeSet
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAndRanges
     */
    public function testAnd_ValidRangeList_ExportReturnsMatchingRangeList(
        array $rangeSet,
        array $anotherRangeSet,
        array $expectedRangeSet
    ): void {
        $actualValue = (new RangeSetCalc)
            ->and(RangeSet::import(...$rangeSet), RangeSet::import(...$anotherRangeSet))
            ->export();
        self::assertEquals($expectedRangeSet, $actualValue);
    }

    public function providerAndRanges(): array
    {
        return [
            "Empty range" => [[[1, 2]], [], []],
            "Empty existing range" => [[], [[1, 2]], []],
            "Range after existing range" => [[[1, 2]], [[4, 5]], []],
            "Range before existing range" => [[[4, 5]], [[1, 2]], []],
            "Range right before existing range" => [[[2, 5]], [[1, 1]], []],
            "Range partially before existing range" => [[[2, 5]], [[1, 3]], [[2, 3]]],
            "Range entirely inside existing range" => [[[2, 5]], [[3, 3]], [[3, 3]]],
            "Range starts before existing range with matching ends" => [[[2, 5]], [[1, 5]], [[2, 5]]],
            "Range starts before and ends after existing range" => [[[2, 5]], [[3, 3]], [[3, 3]]],
            "Range starts before and ends after all existing ranges" =>
                [[[2, 5], [7, 10]], [[1, 13]], [[2, 5], [7, 10]]],
            "Range partially intersects with two existing ranges" =>
                [[[2, 5], [7, 10]], [[3, 7]], [[3, 5], [7, 7]]],
        ];
    }

    /**
     * @param array $rangeSet
     * @param array $anotherRangeSet
     * @param array $expectedRangeSet
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerDiffRanges
     */
    public function testXor_ValidRangeLists_ExportReturnsDiffRangeList(
        array $rangeSet,
        array $anotherRangeSet,
        array $expectedRangeSet
    ): void {
        $actualValue = (new RangeSetCalc)
            ->xor(RangeSet::import(...$rangeSet), RangeSet::import(...$anotherRangeSet))
            ->export();
        self::assertEquals($expectedRangeSet, $actualValue);
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
}
