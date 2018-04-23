<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\TokenBuffer;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\IO\CharBuffer;
use Remorhaz\UniLex\Unicode\CharFactory;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

/**
 * @covers \Remorhaz\UniLex\Lexer\TokenBuffer
 */
class TokenBufferTest extends TestCase
{

    public function testIsEnd_EmptyInputBuffer_ReturnsTrue(): void
    {
        $inputBuffer = CharBuffer::fromString('');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory());
        $actualValue = (new TokenBuffer($reader, new CharFactory))->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyInputBuffer_ReturnsTrue(): void
    {
        $inputBuffer = CharBuffer::fromString('a');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory);
        $actualValue = (new TokenBuffer($reader, new CharFactory))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of buffer at index 0
     */
    public function testNextSymbol_EmptyInputBuffer_ThrowsException(): void
    {
        $inputBuffer = CharBuffer::fromString('');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory);
        (new TokenBuffer($reader, new CharFactory))->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptyInputBuffer_GetSymbolReturnsSecondSymbol(): void
    {
        $inputBuffer = CharBuffer::fromString('ab');
        $reader = new TokenReader($inputBuffer, new Utf8TokenMatcher, new TokenFactory);
        $tokenBuffer = new TokenBuffer($reader, new CharFactory);
        $tokenBuffer->nextSymbol();
        $actualValue = $tokenBuffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }
}
