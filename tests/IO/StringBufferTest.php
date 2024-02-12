<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\IO;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\IO\StringBuffer;

#[CoversClass(StringBuffer::class)]
class StringBufferTest extends TestCase
{
    public function testIsEnd_EmptyBuffer_ReturnsTrue(): void
    {
        $actualValue = (new StringBuffer(''))->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptyBuffer_ReturnsFalse(): void
    {
        $actualValue = (new StringBuffer('a'))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetSymbol_EmptyBuffer_ThrowsException(): void
    {
        $buffer = new StringBuffer('');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('No symbol to preview at index 0');
        $buffer->getSymbol();
    }

    /**
     * @throws UniLexException
     */
    public function testGetSymbol_NotEmptyBuffer_ReturnsFirstValue(): void
    {
        $actualValue = (new StringBuffer('a'))->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testNextSymbol_EmptyBuffer_ThrowsException(): void
    {
        $buffer = new StringBuffer('');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Unexpected end of buffer on preview at index 0');
        $buffer->nextSymbol();
    }

    /**
     * @throws UniLexException
     */
    public function testNextSymbol_NotEmptyBuffer_GetSymbolReturnsSecondValue(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws UniLexException
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
     * @throws UniLexException
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
     * @throws UniLexException
     */
    public function testGetTokenPosition_NextSymbolNotCalled_ReturnsZeroOffsets(): void
    {
        $position = (new StringBuffer('a'))->getTokenPosition();
        self::assertSame(0, $position->getStartOffset());
        self::assertSame(0, $position->getFinishOffset());
    }

    /**
     * @throws UniLexException
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
     * @throws UniLexException
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

    public function testGetTokenAsArray_NextSymbolNotCalled_ReturnsEmptyArray(): void
    {
        $actualValue = (new StringBuffer('a'))->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTokenAsArray_NextSymbolCalledTwice_ReturnsMatchingArray(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->nextSymbol();
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([0x61, 0x62], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTokenAsArray_NextSymbolAndResetTokenCalled_ReturnsEmptyArray(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->resetToken();
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTokenAsArray_NextSymbolAndFinishTokenCalled_ReturnsEmptyArray(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, false));
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    public function testGetTokenAsString_NextSymbolNotCalled_ReturnsEmptyString(): void
    {
        $actualValue = (new StringBuffer('a'))->getTokenAsString();
        self::assertSame('', $actualValue);
    }


    /**
     * @throws UniLexException
     */
    public function testGetTokenAsString_NextSymbolCalledTwice_ReturnsMatchingString(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->nextSymbol();
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('ab', $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTokenAsString_NextSymbolAndResetTokenCalled_ReturnsEmptyString(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->resetToken();
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetTokenAsString_NextSymbolAndFinishTokenCalled_ReturnsEmptyString(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, false));
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testPrevSymbol_ZeroRepeat_ThrowsException(): void
    {
        $buffer = new StringBuffer('');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Non-positive unread repeat counter: 0');
        $buffer->prevSymbol(0);
    }

    /**
     * @throws UniLexException
     */
    public function testPrevSymbol_NegativeRepeat_ThrowsException(): void
    {
        $buffer = new StringBuffer('');

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Non-positive unread repeat counter: -2');
        $buffer->prevSymbol(-2);
    }

    /**
     * @throws UniLexException
     */
    public function testPrevSymbol_RepeatTooLarge_ThrowsException(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Invalid unread repeat counter: 2');
        $buffer->prevSymbol(2);
    }

    /**
     * @throws UniLexException
     */
    public function testPrevSymbol_NextSymbolCalled_GetTokenPositionReturnsZeroFinishOffset(): void
    {
        $buffer = new StringBuffer('ab');
        $buffer->nextSymbol();
        $buffer->prevSymbol();
        $actualValue = $buffer->getTokenPosition()->getFinishOffset();
        self::assertSame(0, $actualValue);
    }
}
