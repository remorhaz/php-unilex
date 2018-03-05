<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\TokenPosition;

/**
 * @covers \Remorhaz\UniLex\TokenPosition
 */
class TokenPositionTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetStartOffset_ConstructWithValue_ReturnsSameValue(): void
    {
        $position = new TokenPosition(0, 1);
        $actualValue = $position->getStartOffset();
        self::assertSame(0, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetFinishOffset_ConstructWithValue_ReturnsSameValue(): void
    {
        $position = new TokenPosition(0, 1);
        $actualValue = $position->getFinishOffset();
        self::assertSame(1, $actualValue);
    }

    /**
     * @param int $startOffset
     * @param int $finishOffset
     * @param int $expectedLength
     * @dataProvider providerOffsetsWithLength
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetLength_Constructed_ReturnsCorrectSize(
        int $startOffset,
        int $finishOffset,
        int $expectedLength
    ): void {
        $position = new TokenPosition($startOffset, $finishOffset);
        $actualValue = $position->getLength();
        self::assertSame($expectedLength, $actualValue);
    }

    public function providerOffsetsWithLength(): array
    {
        return [
            'Single symbol token' => [0, 1, 1],
            'Empty token' => [0, 0, 0],
        ];
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Negative start offset in token position: -1
     */
    public function testConstruct_NegativeStartOffset_ThrowsException()
    {
        new TokenPosition(-1, 1);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Finish offset lesser than start in token position: -1 < 0
     */
    public function testConstruct_FinishOffsetLessThanStart_ThrowsException()
    {
        new TokenPosition(0, -1);
    }
}
