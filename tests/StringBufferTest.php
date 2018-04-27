<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\IO\StringBuffer;

/**
 * @covers \Remorhaz\UniLex\IO\StringBuffer
 */
class StringBufferTest extends TestCase
{

    public function testIsEnd_EmptyBuffer_ReturnsTrue(): void
    {
        $actualValue = (new StringBuffer(''))->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyBuffer_ReturnsFalse(): void
    {
        $actualValue = (new StringBuffer(0x61))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage No symbol to preview at index 0
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_EmptyBuffer_ThrowsException(): void
    {
        (new StringBuffer(''))->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_NotEmptyBuffer_ReturnsFirstValue(): void
    {
        $actualValue = (new StringBuffer('a'))->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of buffer on preview at index 0
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_EmptyBuffer_ThrowsException(): void
    {
        (new StringBuffer(''))->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptyBuffer_GetSymbolReturnsSecondValue(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testResetToken_NextSymbolCalled_GetSymbolReturnsFirstValue(): void
    {
        $buffer = new StringBuffer('ab');
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
        $buffer = new StringBuffer('ab');
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
        $position = (new StringBuffer('a'))->getTokenPosition();
        self::assertSame(0, $position->getStartOffset());
        self::assertSame(0, $position->getFinishOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenPosition_NextSymbolCalled_ReturnsMatchingOffsets(): void
    {
        $buffer = new StringBuffer('a');
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
        $buffer = new StringBuffer('a');
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, true));
        $position = $buffer->getTokenPosition();
        self::assertSame(1, $position->getStartOffset());
        self::assertSame(1, $position->getFinishOffset());
    }

    public function testGetTokenAsArray_EmptyPosition_ReturnsEmptyArray(): void
    {
        $actualValue = (new StringBuffer('a'))->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsArray_NotEmptyPosition_ReturnsMatchingArray(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->nextSymbol();
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([0x61, 0x62], $actualValue);
    }

    public function testGetTokenAsString_EmptyPosition_ReturnsEmptyString(): void
    {
        $actualValue = (new StringBuffer('a'))->getTokenAsString();
        self::assertSame('', $actualValue);
    }


    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NotEmptyPosition_ReturnsMatchingArray(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->nextSymbol();
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('ab', $actualValue);
    }
}
