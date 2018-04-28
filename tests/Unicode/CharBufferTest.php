<?php

namespace Remorhaz\UniLex\Test\Unicode;

use Exception;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\StringBuffer;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Unicode\CharBuffer;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

/**
 * @covers \Remorhaz\UniLex\Unicode\CharBuffer
 */
class CharBufferTest extends TestCase
{

    public function testIsEnd_EmptySourceBuffer_ReturnsTrue(): void
    {
        $source = new StringBuffer('');
        $actualValue = (new CharBuffer($source))->isEnd();
        self::assertTrue($actualValue);
    }

    public function testIsEnd_NotEmptySourceBuffer_ReturnsFalse(): void
    {
        $source = new StringBuffer('a');
        $actualValue = (new CharBuffer($source))->isEnd();
        self::assertFalse($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of source buffer on preview at index 0
     */
    public function testGetSymbol_EmptySourceBuffer_ThrowsException(): void
    {
        $source = new StringBuffer('');
        (new CharBuffer($source))->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_NotEmptySourceBuffer_ReturnsSymbolCode(): void
    {
        $source = new StringBuffer('a');
        $actualValue = (new CharBuffer($source))->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetSymbol_CalledTwiceOneCharInBuffer_ReturnsSameSymbolCode(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $buffer->getSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of source buffer on preview at index 0
     */
    public function testNextSymbol_EmptySourceBuffer_ThrowsException(): void
    {
        $source = new StringBuffer('');
        (new CharBuffer($source))->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testNextSymbol_NotEmptySourceBuffer_GetSymbolReturnsSecondCharCode(): void
    {
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $actualValue = $buffer->getSymbol();
        self::assertSame(0x62, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of source buffer on preview at index 1
     */
    public function testNextSymbol_CalledTwiceOneCharInBuffer_ThrowsException(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $buffer->nextSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Failed to match Unicode char from source buffer
     */
    public function testGetSymbol_MatchFailure_ThrowsException(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $buffer->setMatcher($this->createTokenMatcherThatNeverMatches());
        $buffer->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Invalid Unicode char token
     */
    public function testGetSymbol_TokenFactoryReturnsNonSymbolToken_ThrowsException(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $buffer->setTokenFactory($this->createTokenFactoryThatCreatesInvalidBytesTokens());
        $buffer->getSymbol();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFinishToken_AtZeroOffset_ZeroByteOffsetsInTokenAttributes(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $token = new Token(0, false);
        $buffer->finishToken($token);
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_START));
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_FINISH));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFinishToken_AtZeroOffset_ZeroCharOffsetsInTokenAttributes(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $token = new Token(0, false);
        $buffer->finishToken($token);
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_CHAR_OFFSET_START));
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_CHAR_OFFSET_FINISH));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFinishToken_NextSymbolCalled_MatchingByteOffsetsInTokenAttributes(): void
    {
        $source = new StringBuffer('ж');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $token = new Token(0, false);
        $buffer->finishToken($token);
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_START));
        self::assertSame(2, $token->getAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_FINISH));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testFinishToken_NextSymbolCalled_MatchingCharOffsetsInTokenAttributes(): void
    {
        $source = new StringBuffer('ж');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $token = new Token(0, false);
        $buffer->finishToken($token);
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_CHAR_OFFSET_START));
        self::assertSame(1, $token->getAttribute(TokenAttribute::UNICODE_CHAR_OFFSET_FINISH));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testResetToken_NextSymbolCalled_GetTokenPositionReturnsZeroOffsets(): void
    {
        $source = new StringBuffer('ж');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $buffer->resetToken();
        $position = $buffer->getTokenPosition();
        self::assertSame(0, $position->getStartOffset());
        self::assertSame(0, $position->getFinishOffset());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testResetToken_NextSymbolCalled_FinishTokenSetsZeroByteOffsetsInTokenAttributes(): void
    {
        $source = new StringBuffer('ж');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $buffer->resetToken();
        $token = new Token(0, false);
        $buffer->finishToken($token);
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_START));
        self::assertSame(0, $token->getAttribute(TokenAttribute::UNICODE_BYTE_OFFSET_FINISH));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NextSymbolNotCalled_ReturnsEmptyString(): void
    {
        $source = new StringBuffer('a');
        $buffer = new CharBuffer($source);
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsString_NextSymbolCalledTwice_ReturnsMatchingString(): void
    {
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
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
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
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
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, false));
        $actualValue = $buffer->getTokenAsString();
        self::assertSame('', $actualValue);
    }

    public function testGetTokenAsArray_NextSymbolNotCalled_ReturnsEmptyArray(): void
    {
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetTokenAsArray_NextSymbolCalledTwice_ReturnsMatchingArray(): void
    {
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
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
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
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
        $source = new StringBuffer('ab');
        $buffer = new CharBuffer($source);
        $buffer->nextSymbol();
        $buffer->finishToken(new Token(0, false));
        $actualValue = $buffer->getTokenAsArray();
        self::assertSame([], $actualValue);
    }

    private function createTokenMatcherThatNeverMatches(): TokenMatcherInterface
    {
        return new class implements TokenMatcherInterface
        {

            public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
            {
                return false;
            }

            /**
             * @return Token
             * @throws Exception
             */
            public function getToken(): Token
            {
                throw new Exception("Not implemented");
            }
        };
    }

    private function createTokenFactoryThatCreatesInvalidBytesTokens(): TokenFactoryInterface
    {
        return new class implements TokenFactoryInterface
        {

            public function createToken(int $tokenId): Token
            {
                return new Token(TokenType::INVALID_BYTES, false);
            }

            /**
             * @return Token
             * @throws Exception
             */
            public function createEoiToken(): Token
            {
                throw new Exception("Not implemented");
            }
        };
    }
}
