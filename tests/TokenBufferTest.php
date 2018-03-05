<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\TokenBuffer;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\Unicode\CodeSymbolFactory;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Utf8TokenMatcher;

/**
 * @covers \Remorhaz\UniLex\TokenBuffer
 */
class TokenBufferTest extends TestCase
{

    public function testIsEnd_EmptyInputBuffer_ReturnsTrue(): void
    {
        $inputBuffer = SymbolBuffer::fromString('');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory());
        $actualValue = (new TokenBuffer($reader, new CodeSymbolFactory))->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyInputBuffer_ReturnsTrue(): void
    {
        $inputBuffer = SymbolBuffer::fromString('a');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory);
        $actualValue = (new TokenBuffer($reader, new CodeSymbolFactory))->isEnd();
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
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory);
        (new TokenBuffer($reader, new CodeSymbolFactory))->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptyInputBuffer_GetSymbolReturnsSecondSymbol(): void
    {
        $inputBuffer = SymbolBuffer::fromString('ab');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory);
        $tokenBuffer = new TokenBuffer($reader, new CodeSymbolFactory);
        $tokenBuffer->nextSymbol();
        $actualValue = $tokenBuffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }
}