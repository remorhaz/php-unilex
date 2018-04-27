<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\IO\CharBuffer;

/**
 * @covers \Remorhaz\UniLex\IO\CharBuffer
 */
class CharBufferTest extends TestCase
{

    public function testIsEnd_EmptyString_ReturnsTrue(): void
    {
        $actualValue = (new CharBuffer)->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyString_ReturnsFalse(): void
    {
        $actualValue = (new CharBuffer(0x61))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage No symbol to preview at index 0
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_EmptyString_ThrowsException(): void
    {
        (new CharBuffer)->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_NotEmptyString_ReturnsFirstByte(): void
    {
        $actualValue = (new CharBuffer(0x61))->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of buffer on preview at index 0
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_EmptyString_ThrowsException(): void
    {
        (new CharBuffer)->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptyString_GetSymbolReturnsSecondByte(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testResetToken_NextSymbolCalled_GetSymbolReturnsFirstByte(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->resetToken();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFinishToken_NotAtBufferEnd_GetSymbolAfterResetTokenReturnsSecondByte(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(1, false));
        $buffer->resetToken();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }
}
