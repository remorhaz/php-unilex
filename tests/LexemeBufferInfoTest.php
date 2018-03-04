<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\LexemeBufferInfo;

/**
 * @covers \Remorhaz\UniLex\LexemeBufferInfo
 */
class LexemeBufferInfoTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetPosition_ConstructWithValue_ReturnsPositionWithSameStartOffset(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $position = new LexemePosition(0, 1);
        $info = new LexemeBufferInfo($buffer, $position);
        $actualValue = $info->getPosition();
        self::assertSame(0, $actualValue->getStartOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetPosition_ConstructWithValue_ReturnsPositionWithSameFinishOffset(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $position = new LexemePosition(0, 1);
        $info = new LexemeBufferInfo($buffer, $position);
        $actualValue = $info->getPosition();
        self::assertSame(1, $actualValue->getFinishOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testExtractLexeme_ConstructWithValue_ReturnsEqualValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $info = new LexemeBufferInfo($buffer, new LexemePosition(0, 1));
        $actualValue = $info->extract();
        $expectedValue = \SplFixedArray::fromArray([0x61]);
        self::assertEquals($expectedValue, $actualValue);
    }
}
