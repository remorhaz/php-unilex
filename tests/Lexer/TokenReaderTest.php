<?php

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\IO\StringBuffer;
use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

/**
 * @covers \Remorhaz\UniLex\Lexer\TokenReader
 */
class TokenReaderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferStart_ReturnsMatchingSymbolToken(): void
    {
        $buffer = new StringBuffer('a');
        $tokenFactory = new TokenFactory;
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, $tokenFactory);
        $token = $scanner->read();
        self::assertSame(TokenType::SYMBOL, $token->getType());
        self::assertSame(0x61, $token->getAttribute(TokenAttribute::UNICODE_CHAR));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferMiddle_ReturnsMatchingSymbolToken(): void
    {
        $buffer = new StringBuffer('ab');
        $tokenFactory = new TokenFactory;
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, $tokenFactory);
        $scanner->read();
        $token = $scanner->read();
        self::assertSame(TokenType::SYMBOL, $token->getType());
        self::assertSame(0x62, $token->getAttribute(TokenAttribute::UNICODE_CHAR));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyBufferEnd_ReturnsEoiToken(): void
    {
        $buffer = new StringBuffer('a');
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, new TokenFactory);
        $scanner->read();
        $actualValue = $scanner->read()->isEoi();
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_EmptyBuffer_ReturnsEoiToken(): void
    {
        $buffer = new StringBuffer('');
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, new TokenFactory);
        $actualValue = $scanner->read()->isEoi();
        self::assertTrue($actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Buffer end reached
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_AfterBufferEnd_ThrowsException(): void
    {
        $buffer = new StringBuffer('');
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, new TokenFactory);
        $scanner->read();
        $scanner->read();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyInvalidBufferStart_ReturnsMatchingToken(): void
    {
        $buffer = new StringBuffer("\x80");
        $tokenFactory = new TokenFactory;
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, $tokenFactory);
        $token = $scanner->read();
        self::assertSame(TokenType::INVALID_BYTES, $token->getType());
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected character at position 0
     */
    public function testRead_MatcherFailsNotAtEnd_ThrowsException(): void
    {
        $buffer = new StringBuffer("abc");
        $tokenFactory = new TokenFactory;
        $scanner = new TokenReader($buffer, $this->createFailingMatcher(), $tokenFactory);
        $scanner->read();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Unexpected end of input at position 3
     */
    public function testRead_MatcherFailsAtEnd_ThrowsException(): void
    {
        $buffer = new StringBuffer("abc");
        $tokenFactory = new TokenFactory;
        $scanner = new TokenReader($buffer, $this->createFailingAtEndMatcher(), $tokenFactory);
        $scanner->read();
    }

    private function createFailingMatcher(): TokenMatcherInterface
    {
        return new class implements TokenMatcherInterface
        {
            public function getToken(): Token
            {
                return new Token(0, false);
            }

            public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
            {
                return false;
            }
        };
    }

    private function createFailingAtEndMatcher(): TokenMatcherInterface
    {
        return new class implements TokenMatcherInterface
        {
            public function getToken(): Token
            {
                return new Token(0, false);
            }

            public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
            {
                while (!$buffer->isEnd()) {
                    $buffer->nextSymbol();
                }
                return false;
            }
        };
    }
}
