<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\TokenPosition;
use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\TokenBufferInfo;

/**
 * @covers \Remorhaz\UniLex\TokenBufferInfo
 */
class TokenBufferInfoTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetPosition_ConstructWithValue_ReturnsPositionWithSameStartOffset(): void
    {
        $buffer = CharBuffer::fromString('a');
        $position = new TokenPosition(0, 1);
        $info = new TokenBufferInfo($buffer, $position);
        $actualValue = $info->getPosition();
        self::assertSame(0, $actualValue->getStartOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetPosition_ConstructWithValue_ReturnsPositionWithSameFinishOffset(): void
    {
        $buffer = CharBuffer::fromString('a');
        $position = new TokenPosition(0, 1);
        $info = new TokenBufferInfo($buffer, $position);
        $actualValue = $info->getPosition();
        self::assertSame(1, $actualValue->getFinishOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testExtract_ConstructWithValue_ReturnsEqualValue(): void
    {
        $buffer = CharBuffer::fromString('a');
        $info = new TokenBufferInfo($buffer, new TokenPosition(0, 1));
        $actualValue = $info->extract();
        $expectedValue = \SplFixedArray::fromArray([0x61]);
        self::assertEquals($expectedValue, $actualValue);
    }
}
