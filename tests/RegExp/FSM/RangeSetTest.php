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
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testImport_NoArguments_GetRangesReturnsEmptyArray(): void
    {
        $actualValue = RangeSet::import()->getRanges();
        self::assertEquals([], $actualValue);
    }

    /**
     * @param array $ranges
     * @param array $expectedRanges
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAddableRanges
     */
    public function testExport_ConstructWithRanges_ReturnsMergedRanges(array $ranges, array $expectedRanges): void
    {
        $actualValue = (new RangeSet(...Range::importList(...$ranges)))->export();
        self::assertEquals($expectedRanges, $actualValue);
    }

    /**
     * @param array $ranges
     * @param array $expectedRanges
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAddableRanges
     */
    public function testImport_ValidRanges_ExportReturnsMergedRanges(array $ranges, array $expectedRanges): void
    {
        $actualValue = RangeSet::import(...$ranges)->export();
        self::assertEquals($expectedRanges, $actualValue);
    }

    /**
     * @param array $ranges
     * @param array $expectedRanges
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAddableRanges
     */
    public function testAddRange_ValidRanges_ExportReturnsMergedRanges(array $ranges, array $expectedRanges): void
    {
        $rangeSet = new RangeSet;
        $rangeSet->addRange(...Range::importList(...$ranges));
        $actualValue = $rangeSet->export();
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
    public function testAddRange_CalledTwice_ExportReturnsMergedRanges(
        array $firstRanges,
        array $secondRanges,
        array $expectedRanges
    ) {
        $rangeSet = new RangeSet;
        $rangeSet->addRange(...Range::importList(...$firstRanges));
        $rangeSet->addRange(...Range::importList(...$secondRanges));
        $actualValue = $rangeSet->export();
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
    public function testGetDiffRange_ValidRangeLists_ExportReturnsDiffRangeList(
        array $firstRanges,
        array $secondRanges,
        array $expectedDiff
    ): void {
        $actualValue = RangeSet::import(...$firstRanges)
            ->getDiff(...Range::importList(...$secondRanges))
            ->export();
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

    /**
     * @param array $firstRanges
     * @param array $secondRanges
     * @param array $expectedRange
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerAndRanges
     */
    public function testGetAnd_ValidRangeList_ExportReturnsMatchingRangeList(
        array $firstRanges,
        array $secondRanges,
        array $expectedRange
    ): void {
        $actualValue = RangeSet::import(...$firstRanges)
            ->getAnd(...Range::importList(...$secondRanges))
            ->export();
        self::assertEquals($expectedRange, $actualValue);
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
}
