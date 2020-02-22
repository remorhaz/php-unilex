<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\RangeSet
 */
class RangeSetTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testIsEmpty_NoRangeAdded_ReturnsTrue(): void
    {
        $actualValue = (new RangeSet())->isEmpty();
        self::assertTrue($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsEmpty_RangeAdded_ReturnsFalse(): void
    {
        $rangeSet = new RangeSet();
        $rangeSet->addRange(new Range(1, 2));
        $actualValue = $rangeSet->isEmpty();
        self::assertFalse($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testGetRanges_ConstructedWithoutArguments_ReturnsEmptyArray(): void
    {
        $actualValue = (new RangeSet())->getRanges();
        self::assertEquals([], $actualValue);
    }

    /**
     * @throws Exception
     */
    public function testImport_NoArguments_GetRangesReturnsEmptyArray(): void
    {
        $actualValue = RangeSet::import()->getRanges();
        self::assertEquals([], $actualValue);
    }

    /**
     * @param array $ranges
     * @param array $expectedRanges
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     * @dataProvider providerAddableRanges
     */
    public function testAddRange_ValidRanges_ExportReturnsMergedRanges(array $ranges, array $expectedRanges): void
    {
        $rangeSet = new RangeSet();
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
            "First range contains another's start" => [[[1, 3], [2, 4]], [[1, 4]]],
            "First range contains another's finish" => [[[2, 4], [1, 3]], [[1, 4]]],
        ];
    }

    /**
     * @param array $firstRanges
     * @param array $secondRanges
     * @param array $expectedRanges
     * @throws Exception
     * @dataProvider providerDoubleAddableRanges
     */
    public function testAddRange_CalledTwice_ExportReturnsMergedRanges(
        array $firstRanges,
        array $secondRanges,
        array $expectedRanges
    ) {
        $rangeSet = new RangeSet();
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
            "First range contains another's start" => [[[1, 3]], [[2, 4]], [[1, 4]]],
            "First range contains another's finish" => [[[2, 4]], [[1, 3]], [[1, 4]]],
        ];
    }
}
