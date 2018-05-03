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

    public function testIsEnd_EmptyBuffer_ReturnsTrue(): void
    {
        $actualValue = (new CharBuffer)->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyBuffer_ReturnsFalse(): void
    {
        $actualValue = (new CharBuffer(0x61))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage No symbol to preview at index 0
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_EmptyBuffer_ThrowsException(): void
    {
        (new CharBuffer)->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_NotEmptyBuffer_ReturnsFirstValue(): void
    {
        $actualValue = (new CharBuffer(0x61))->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of buffer on preview at index 0
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_EmptyBuffer_ThrowsException(): void
    {
        (new CharBuffer)->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptyBuffer_GetSymbolReturnsSecondValue(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testResetToken_NextSymbolCalled_GetSymbolReturnsFirstValue(): void
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
    public function testFinishToken_NotAtBufferEnd_GetSymbolAfterResetTokenReturnsSecondValue(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(1, false));
        $buffer->resetToken();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenPosition_NextSymbolNotCalled_ReturnsZeroOffsets(): void
    {
        $position = (new CharBuffer(0x61))->getTokenPosition();
        self::assertSame(0, $position->getStartOffset());
        self::assertSame(0, $position->getFinishOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenPosition_NextSymbolCalled_ReturnsMatchingOffsets(): void
    {
        $buffer = new CharBuffer(0x61);
        $buffer->nextSymbol();
        $position = $buffer->getTokenPosition();
        self::assertSame(0, $position->getStartOffset());
        self::assertSame(1, $position->getFinishOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenPosition_NextSymbolAndFinishTokenCalled_ReturnsMatchingOffsets(): void
    {
        $buffer = new CharBuffer(0x61);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, true));
        $position = $buffer->getTokenPosition();
        self::assertSame(1, $position->getStartOffset());
        self::assertSame(1, $position->getFinishOffset());
    }

    public function testGetTokenAsArray_NextSymbolNotCalled_ReturnsEmptyArray(): void
    {
        $actualValue = (new CharBuffer(0x61))->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsArray_NextSymbolCalledTwice_ReturnsMatchingArray(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->nextSymbol();
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([0x61, 0x62], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsArray_NextSymbolAndResetTokenCalled_ReturnsEmptyArray(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->resetToken();
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsArray_NextSymbolAndFinishTokenCalled_ReturnsEmptyArray(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, false));
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NextSymbolNotCalled_ReturnsEmptyString(): void
    {
        $actualValue = (new CharBuffer(0x61))->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NextSymbolCalledTwice_ReturnsMatchingString(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->nextSymbol();
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('ab', $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NextSymbolAndResetTokenCalled_ReturnsEmptyString(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->resetToken();
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NextSymbolAndFinishTokenCalled_ReturnsEmptyString(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, false));
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Only 8-bit symbols can be converted to string, 256 found at index 0
     */
    public function testGetTokenAsString_Not8BitSymbolAtPosition_ThrowsException(): void
    {
        $buffer = new CharBuffer(0x100);
        $buffer->nextSymbol();
        $buffer->getTokenAsString();
    }


    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Non-positive unread repeat counter: 0
     */
    public function testPrevSymbol_ZeroRepeat_ThrowsException(): void
    {
        $buffer = new CharBuffer();
        $buffer->prevSymbol(0);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Non-positive unread repeat counter: -2
     */
    public function testPrevSymbol_NegativeRepeat_ThrowsException(): void
    {
        $buffer = new CharBuffer();
        $buffer->prevSymbol(-2);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid unread repeat counter: 2
     */
    public function testPrevSymbol_RepeatTooLarge_ThrowsException(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->prevSymbol(2);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testPrevSymbol_NextSymbolCalled_GetTokenPositionReturnsZeroFinishOffset(): void
    {
        $buffer = new CharBuffer(0x61, 0x62);
        $buffer->nextSymbol();
        $buffer->prevSymbol();
        $actualValue = $buffer->getTokenPosition()->getFinishOffset();
        self::assertSame(0, $actualValue);
    }
}
