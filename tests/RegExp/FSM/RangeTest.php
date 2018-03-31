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
}
