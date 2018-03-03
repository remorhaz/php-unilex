<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\LexemeFactory;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\Unicode\InvalidBytesLexeme;
use Remorhaz\UniLex\SymbolBufferLexemeReader;
use Remorhaz\UniLex\Unicode\SymbolLexeme;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;

/**
 * @covers \Remorhaz\UniLex\SymbolBufferLexemeReader
 */
class SymbolBufferLexemeReaderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferStart_ReturnsMatchingSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $grammar = new ContextFreeGrammar(1, 2);
        $lexemeFactory = new LexemeFactory($grammar);
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, new LexemePosition(0, 1));
        $expectedValue = new SymbolLexeme($lexemeInfo, 0x00000061);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferMiddle_ReturnsMatchingSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('ab');
        $grammar = new ContextFreeGrammar(1, 2);
        $lexemeFactory = new LexemeFactory($grammar);
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, new LexemePosition(1, 2));
        $expectedValue = new SymbolLexeme($lexemeInfo, 0x00000062);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
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
        $grammar = new ContextFreeGrammar(1, 2);
        $grammar->addToken($grammar->getEoiSymbol(), 1);
        $lexemeFactory = new LexemeFactory($grammar);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
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
        $grammar = new ContextFreeGrammar(1, 2);
        $grammar->addToken($grammar->getEoiSymbol(), 1);
        $lexemeFactory = new LexemeFactory($grammar);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
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
        $grammar = new ContextFreeGrammar(1, 2);
        $grammar->addToken($grammar->getEoiSymbol(), 1);
        $lexemeFactory = new LexemeFactory($grammar);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $scanner->read();
        $scanner->read();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyInvalidBufferStart_ReturnsMatch(): void
    {
        $buffer = SymbolBuffer::fromString("\x80");
        $grammar = new ContextFreeGrammar(1, 2);
        $lexemeFactory = new LexemeFactory($grammar);
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, new LexemePosition(0, 1));
        $expectedValue = new InvalidBytesLexeme($lexemeInfo, 0x80);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher, $lexemeFactory);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }
}
