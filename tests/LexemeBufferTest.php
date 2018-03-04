<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LexemeBuffer;
use Remorhaz\UniLex\LexemeReader;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\Unicode\CodeSymbolFactory;
use Remorhaz\UniLex\Unicode\LexemeFactory;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;

/**
 * @covers \Remorhaz\UniLex\LexemeBuffer
 */
class LexemeBufferTest extends TestCase
{

    public function testIsEnd_EmptyInputBuffer_ReturnsTrue(): void
    {
        $inputBuffer = SymbolBuffer::fromString('');
        $reader = new LexemeReader($inputBuffer, new Utf8LexemeMatcher, new LexemeFactory());
        $actualValue = (new LexemeBuffer($reader, new CodeSymbolFactory))->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyInputBuffer_ReturnsTrue(): void
    {
        $inputBuffer = SymbolBuffer::fromString('a');
        $reader = new LexemeReader($inputBuffer, new Utf8LexemeMatcher, new LexemeFactory);
        $actualValue = (new LexemeBuffer($reader, new CodeSymbolFactory))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of buffer at index 0
     */
    public function testNextSymbol_EmptyInputBuffer_ThrowsException(): void
    {
        $inputBuffer = SymbolBuffer::fromString('');
        $reader = new LexemeReader($inputBuffer, new Utf8LexemeMatcher, new LexemeFactory);
        (new LexemeBuffer($reader, new CodeSymbolFactory))->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptyInputBuffer_GetSymbolReturnsSecondSymbol(): void
    {
        $inputBuffer = SymbolBuffer::fromString('ab');
        $reader = new LexemeReader($inputBuffer, new Utf8LexemeMatcher, new LexemeFactory);
        $lexemeBuffer = new LexemeBuffer($reader, new CodeSymbolFactory);
        $lexemeBuffer->nextSymbol();
        $actualValue = $lexemeBuffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }
}