<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\EofLexeme;
use Remorhaz\UniLex\Unicode\InvalidBytesLexeme;
use Remorhaz\UniLex\SymbolBufferLexemeReader;
use Remorhaz\UniLex\Unicode\SymbolLexeme;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;

class SymbolBufferLexemeReaderTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferStart_ReturnsMatchingSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $expectedValue = new SymbolLexeme($lexemeInfo, 0x00000061);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyValidBufferMiddle_ReturnsMatchingSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('ab');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 1, 2);
        $expectedValue = new SymbolLexeme($lexemeInfo, 0x00000062);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher);
        $scanner->read();
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyBufferEnd_ReturnsEofLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 1, 1);
        $expectedValue = new EofLexeme($lexemeInfo);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher);
        $scanner->read();
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_EmptyBuffer_ReturnsEofLexeme(): void
    {
        $buffer = SymbolBuffer::fromString('');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, 0);
        $expectedValue = new EofLexeme($lexemeInfo);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }

    /**
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Buffer end reached
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_AfterBufferEnd_ThrowsException(): void
    {
        $buffer = SymbolBuffer::fromString('');
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher);
        $scanner->read();
        $scanner->read();
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testRead_NotEmptyInvalidBufferStart_ReturnsMatch(): void
    {
        $buffer = SymbolBuffer::fromString("\x80");
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $expectedValue = new InvalidBytesLexeme($lexemeInfo);
        $scanner = new SymbolBufferLexemeReader($buffer, new Utf8LexemeMatcher);
        $actualValue = $scanner->read();
        self::assertEquals($expectedValue, $actualValue);
    }
}
