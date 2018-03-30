<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
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
        $actualValue = (new RangeSet(...$ranges))->getRanges();
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
        $rangeSet->addRange(...$ranges);
        $actualValue = $rangeSet->getRanges();
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
        $rangeSet->addRange(...$firstRanges);
        $rangeSet->addRange(...$secondRanges);
        $actualValue = $rangeSet->getRanges();
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
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid range 2..1
     */
    public function testAddRange_InvalidRange_ThrowsException(): void
    {
        (new RangeSet)->addRange([2, 1]);
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
        $actualValue = (new RangeSet(...$firstRanges))
            ->getDiff(...$secondRanges)
            ->getRanges();
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
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid range 3..2
     */
    public function testGetDiffRange_InvalidRange_ThrowsException(): void
    {
        (new RangeSet([1, 2]))->getDiff([3, 2]);
    }
}
