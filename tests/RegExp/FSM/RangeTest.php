<?php

namespace Remorhaz\UniLex\Test\RegExp\FSM;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\FSM\Range;

/**
 * @covers \Remorhaz\UniLex\RegExp\FSM\Range
 */
class RangeTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid range 2..1
     */
    public function testConstruct_FinishGreaterThanStart_ThrowsException(): void
    {
        new Range(2, 1);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetStart_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new Range(1, 2))->getStart();
        self::assertSame(1, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetFinish_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new Range(1, 2))->getFinish();
        self::assertSame(2, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testToString_FinishEqualsStart_ShowsOnlyStart(): void
    {
        $actualValue = (string) new Range(1);
        self::assertSame("1", $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testToString_FinishNotEqualsStart_ShowsOnlyStart(): void
    {
        $actualValue = (string) new Range(1, 2);
        self::assertSame("1..2", $actualValue);
    }

    /**
     * @param int $start
     * @param int $finish
     * @param int $char
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerCharsInRange
     */
    public function testContainsChar_CharInRange_ReturnsTrue(int $start, int $finish, int $char): void
    {
        $actualValue = (new Range($start, $finish))->containsChar($char);
        self::assertTrue($actualValue);
    }

    public function providerCharsInRange(): array
    {
        return [
            "Char matches single-char range" => [1, 1, 1],
            "Char matches start of range" => [1, 2, 1],
            "Char matches finish of range" => [1, 2, 2],
            "Char is in the middle of range" => [1, 3, 2],
        ];
    }

    /**
     * @param int $start
     * @param int $finish
     * @param int $char
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerCharsNotInRange
     */
    public function testContainsChar_CharNotInRange_ReturnsFalse(int $start, int $finish, int $char): void
    {
        $actualValue = (new Range($start, $finish))->containsChar($char);
        self::assertFalse($actualValue);
    }

    public function providerCharsNotInRange(): array
    {
        return [
            "Char less than range start" => [1, 2, 0],
            "Char greater than range finish" => [1, 2, 3],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStartsBeforeStartOf_StartsBeforeStartOfValue_ReturnsTrue(): void
    {
        $range = new Range(1, 2);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->startsBeforeStartOf($anotherRange);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStartsBeforeStartOf_StartsAtStartOfValue_ReturnsFalse(): void
    {
        $range = new Range(1, 2);
        $anotherRange = new Range(1, 3);
        $actualValue = $range->startsBeforeStartOf($anotherRange);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testStartsBeforeStartOf_StartsAfterStartOfValue_ReturnsFalse(): void
    {
        $range = new Range(2, 3);
        $anotherRange = new Range(1, 2);
        $actualValue = $range->startsBeforeStartOf($anotherRange);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEndsBeforeStartOf_EndsBeforeStartOfValue_ReturnsTrue(): void
    {
        $range = new Range(1, 2);
        $anotherRange = new Range(3, 4);
        $actualValue = $range->startsBeforeStartOf($anotherRange);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEndsBeforeStartOf_EndsAtStartOfValue_ReturnsFalse(): void
    {
        $range = new Range(1, 2);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->endsBeforeStartOf($anotherRange);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEndsBeforeStartOf_EndsAfterStartOfValue_ReturnsFalse(): void
    {
        $range = new Range(1, 2);
        $anotherRange = new Range(1, 2);
        $actualValue = $range->endsBeforeStartOf($anotherRange);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEndsBeforeFinishOf_EndsBeforeFinishOfValue_ReturnsTrue(): void
    {
        $range = new Range(1, 2);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->endsBeforeFinishOf($anotherRange);
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEndsBeforeFinishOf_EndsAtFinishOfValue_ReturnsFalse(): void
    {
        $range = new Range(1, 3);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->endsBeforeFinishOf($anotherRange);
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testEndsBeforeFinishOf_EndsAfterFinishOfValue_ReturnsFalse(): void
    {
        $range = new Range(1, 4);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->endsBeforeFinishOf($anotherRange);
        self::assertFalse($actualValue);
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerIntersectingRanges
     */
    public function testIntersects_ValuesIntersect_ReturnsTrue(array $rangeData, array $anotherRangeData): void
    {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->intersects($anotherRange);
        self::assertTrue($actualValue);
    }

    public function providerIntersectingRanges(): array
    {
        return [
            "Partially overlapping ranges" => [[1, 3], [2, 4]],
            "Ranges with overlapping edges" => [[1, 2], [2, 3]],
            "Strictly matching ranges" => [[1, 2], [1, 2]],
            "One range contains another" => [[1, 4], [2, 3]],
            "One range is contained by another" => [[2, 3], [1, 4]],
            "One range contains another with same start" => [[1, 4], [1, 3]],
            "One range contains another with same finish" => [[1, 4], [2, 4]],
        ];
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerNotIntersectingRanges
     */
    public function testIntersects_ValuesNotIntersect_ReturnsFalse(array $rangeData, array $anotherRangeData): void
    {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->intersects($anotherRange);
        self::assertFalse($actualValue);
    }

    public function providerNotIntersectingRanges(): array
    {
        return [
            "One range before another" => [[1, 2], [4, 5]],
            "One range after another" => [[4, 5], [1, 2]],
            "One range follows another" => [[1, 2], [3, 4]],
            "One range precedes another" => [[3, 4], [1, 2]],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCopyBeforeStartOf_PartiallyOverlappingRanges_ReturnsMatchingPartOfRange(): void
    {
        $range = new Range(1, 3);
        $anotherRange = new Range(2, 4);
        $actualValue = $range->copyBeforeStartOf($anotherRange)->export();
        self::assertEquals([1, 1], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCopyBeforeFinishOf_PartiallyOverlappingRanges_ReturnsMatchingPartOfRange(): void
    {
        $range = new Range(1, 4);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->copyBeforeFinishOf($anotherRange)->export();
        self::assertEquals([1, 3], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testCopyAfterFinishOf_PartiallyOverlappingRanges_ReturnsMatchingPartOfRange(): void
    {
        $range = new Range(1, 4);
        $anotherRange = new Range(2, 3);
        $actualValue = $range->copyAfterFinishOf($anotherRange)->export();
        self::assertEquals([4, 4], $actualValue);
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerStartInRange
     */
    public function testContainsStartOf_ValueWithStartInRange_ReturnsTrue(
        array $rangeData,
        array $anotherRangeData
    ): void {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->containsStartOf($anotherRange);
        self::assertTrue($actualValue);
    }

    public function providerStartInRange(): array
    {
        return [
            "Start in the middle of range" => [[1, 3], [2, 4]],
            "Start matches start of range" => [[1, 3], [1, 4]],
            "Start matches finish of range" => [[1, 3], [3, 4]],
        ];
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerStartNotInRange
     */
    public function testContainsStartOf_ValueWithStartNotInRange_ReturnsFalse(
        array $rangeData,
        array $anotherRangeData
    ): void {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->containsStartOf($anotherRange);
        self::assertFalse($actualValue);
    }

    public function providerStartNotInRange(): array
    {
        return [
            "Start before range" => [[2, 3], [1, 4]],
            "Start after range" => [[2, 3], [4, 4]],
        ];
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerFinishInRange
     */
    public function testContainsFinishOf_ValueWithFinishInRange_ReturnsTrue(
        array $rangeData,
        array $anotherRangeData
    ): void {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->containsFinishOf($anotherRange);
        self::assertTrue($actualValue);
    }

    public function providerFinishInRange(): array
    {
        return [
            "Finish in the middle of range" => [[1, 3], [1, 2]],
            "Finish matches start of range" => [[1, 3], [1, 1]],
            "Finish matches finish of range" => [[1, 3], [2, 3]],
        ];
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerFinishNotInRange
     */
    public function testContainsFinishOf_ValueWithFinishNotInRange_ReturnsFalse(
        array $rangeData,
        array $anotherRangeData
    ): void {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->containsFinishOf($anotherRange);
        self::assertFalse($actualValue);
    }

    public function providerFinishNotInRange(): array
    {
        return [
            "Finish before range" => [[3, 4], [1, 2]],
            "Finish after range" => [[2, 3], [2, 4]],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testAlignStart_ValidRange_RangeHasMatchingStartAndSameFinish(): void
    {
        $range = new Range(2, 4);
        $anotherRange = new Range(1, 3);
        $actualValue = $range->copyAfterStartOf($anotherRange)->export();
        self::assertEquals([1, 4], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid range 4..3
     */
    public function testAlignStart_ArgumentStartGreaterThanRangeFinish_ThrowsException(): void
    {
        $range = new Range(1, 3);
        $anotherRange = new Range(4, 5);
        $range->copyAfterStartOf($anotherRange);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFollows_RangeFollows_ReturnsTrue(): void
    {
        $range = new Range(3, 4);
        $anotherRange = new Range(1, 2);
        $actualValue = $range->follows($anotherRange);
        self::assertTrue($actualValue);
    }

    /**
     * @param array $rangeData
     * @param array $anotherRangeData
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerRangesNotFollow
     */
    public function testFollows_RangeNotFollows_ReturnsFalse(array $rangeData, array $anotherRangeData): void
    {
        $range = new Range(...$rangeData);
        $anotherRange = new Range(...$anotherRangeData);
        $actualValue = $range->follows($anotherRange);
        self::assertFalse($actualValue);
    }

    public function providerRangesNotFollow(): array
    {
        return [
            "Overlapping ranges" => [[3, 5], [2, 4]],
            "Ranges with gap between" => [[4, 5], [1, 2]],
            "One range precedes another" => [[1, 2], [3, 4]],
        ];
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testImportList_NoArguments_ReturnsEmptyArray(): void
    {
        $actualValue = Range::importList();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testImportList_DataForTwoRanges_ReturnsMatchingRanges(): void
    {
        $rangeList = Range::importList([2, 3], [5, 6]);
        $actualValue = [];
        foreach ($rangeList as $range) {
            self::assertInstanceOf(Range::class, $range);
            $actualValue[] = $range->export();
        }
        self::assertEquals([[2, 3], [5, 6]], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testExport_Constructed_ReturnsMatchingData(): void
    {
        $actualValue = (new Range(3, 5))->export();
        self::assertEquals([3, 5], $actualValue);
    }
}
