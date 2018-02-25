<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class SymbolBufferLexemeInfoTest extends TestCase
{

    public function testGetPosition_ConstructWithValue_ReturnsPositionWithSameStartOffset(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $position = new LexemePosition(0, 1);
        $info = new SymbolBufferLexemeInfo($buffer, $position);
        $actualValue = $info->getPosition();
        self::assertSame(0, $actualValue->getStartOffset());
    }

    public function testGetPosition_ConstructWithValue_ReturnsPositionWithSameFinishOffset(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $position = new LexemePosition(0, 1);
        $info = new SymbolBufferLexemeInfo($buffer, $position);
        $actualValue = $info->getPosition();
        self::assertSame(1, $actualValue->getFinishOffset());
    }

    public function testExtractLexeme_ConstructWithValue_ReturnsEqualValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $info = new SymbolBufferLexemeInfo($buffer, new LexemePosition(0, 1));
        $actualValue = $info->extract();
        $expectedValue = \SplFixedArray::fromArray([0x61]);
        self::assertEquals($expectedValue, $actualValue);
    }
}
