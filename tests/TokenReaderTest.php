<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\SymbolInfo;
use Remorhaz\UniLex\Unicode\Utf8TokenMatcher;

/**
 * @covers \Remorhaz\UniLex\TokenReader
 */
class TokenReaderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferStart_ReturnsMatchingSymbolToken(): void
    {
        $buffer = CharBuffer::fromString('a');
        $tokenFactory = new TokenFactory;
        $matcherInfo = new SymbolInfo(0x00000061);
        $expectedValue = $tokenFactory->createToken(TokenType::SYMBOL);
        $expectedValue->setMatcherInfo($matcherInfo);
        $expectedValue->setAttribute('buffer.position.start', 0);
        $expectedValue->setAttribute('buffer.position.finish', 1);
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, $tokenFactory);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferMiddle_ReturnsMatchingSymbolToken(): void
    {
        $buffer = CharBuffer::fromString('ab');
        $tokenFactory = new TokenFactory;
        $matcherInfo = new SymbolInfo(0x00000062);
        $expectedValue = $tokenFactory->createToken(TokenType::SYMBOL);
        $expectedValue->setMatcherInfo($matcherInfo);
        $expectedValue->setAttribute('buffer.position.start', 1);
        $expectedValue->setAttribute('buffer.position.finish', 2);
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, $tokenFactory);
        $scanner->read();
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyBufferEnd_ReturnsEoiToken(): void
    {
        $buffer = CharBuffer::fromString('a');
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
        $buffer = CharBuffer::fromString('');
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
        $buffer = CharBuffer::fromString('');
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, new TokenFactory);
        $scanner->read();
        $scanner->read();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyInvalidBufferStart_ReturnsMatch(): void
    {
        $buffer = CharBuffer::fromString("\x80");
        $tokenFactory = new TokenFactory;
        $expectedValue = $tokenFactory->createToken(TokenType::INVALID_BYTES);
        $expectedValue->setAttribute('buffer.position.start', 0);
        $expectedValue->setAttribute('buffer.position.finish', 1);
        $scanner = new TokenReader($buffer, new Utf8TokenMatcher, $tokenFactory);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }
}
