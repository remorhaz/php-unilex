<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class SymbolBufferLexemeInfoTest extends TestCase
{

    public function testGetStartOffset_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $info = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $actualValue = $info->getStartOffset();
        self::assertSame(0, $actualValue);
    }

    public function testGetFinishOffset_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $info = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $actualValue = $info->getFinishOffset();
        self::assertSame(1, $actualValue);
    }

    public function testExtractLexeme_ConstructWithValue_ReturnsEqualValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $info = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $actualValue = $info->extract();
        $expectedValue = \SplFixedArray::fromArray([0x61]);
        self::assertEquals($expectedValue, $actualValue);
    }
}
