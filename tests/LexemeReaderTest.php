<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\LexemeBufferInfo;
use Remorhaz\UniLex\LexemeReader;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;
use Remorhaz\UniLex\Unicode\Grammar\LexemeFactory;
use Remorhaz\UniLex\Unicode\SymbolInfo;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;

/**
 * @covers \Remorhaz\UniLex\LexemeReader
 */
class LexemeReaderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferStart_ReturnsMatchingSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeFactory = new LexemeFactory;
        $lexemeInfo = new LexemeBufferInfo($buffer, new LexemePosition(0, 1));
        $matcherInfo = new SymbolInfo(0x00000061);
        $expectedValue = $lexemeFactory->createLexeme(TokenType::SYMBOL);
        $expectedValue->setBufferInfo($lexemeInfo);
        $expectedValue->setMatcherInfo($matcherInfo);
        $scanner = new LexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferMiddle_ReturnsMatchingSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('ab');
        $lexemeFactory = new LexemeFactory;
        $lexemeInfo = new LexemeBufferInfo($buffer, new LexemePosition(1, 2));
        $matcherInfo = new SymbolInfo(0x00000062);
        $expectedValue = $lexemeFactory->createLexeme(TokenType::SYMBOL);
        $expectedValue->setBufferInfo($lexemeInfo);
        $expectedValue->setMatcherInfo($matcherInfo);
        $scanner = new LexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $scanner->read();
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyBufferEnd_ReturnsEoiLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeFactory = new LexemeFactory;
        $scanner = new LexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $scanner->read();
        $actualValue = $scanner->read()->isEoi();
        self::assertTrue($actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_EmptyBuffer_ReturnsEoiLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('');
        $lexemeFactory = new LexemeFactory;
        $scanner = new LexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
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
        $buffer = SymbolBuffer::fromString('');
        $lexemeFactory = new LexemeFactory;
        $scanner = new LexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $scanner->read();
        $scanner->read();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyInvalidBufferStart_ReturnsMatch(): void
    {
        $buffer = SymbolBuffer::fromString("\x80");
        $lexemeFactory = new LexemeFactory;
        $lexemeInfo = new LexemeBufferInfo($buffer, new LexemePosition(0, 1));
        $expectedValue = $lexemeFactory->createLexeme(TokenType::INVALID_BYTES);
        $expectedValue->setBufferInfo($lexemeInfo);
        $scanner = new LexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }
}