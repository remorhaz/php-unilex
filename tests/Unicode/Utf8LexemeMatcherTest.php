<?php

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Unicode\InvalidBytesLexeme;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\Unicode\SymbolLexeme;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;
use Remorhaz\UniLex\Unicode\LexemeListenerInterface;

class Utf8LexemeMatcherTest extends TestCase
{

    /**
     * @param string $text
     * @param int $expectedFinishOffset
     * @param int $expectedSymbol
     * @dataProvider providerValidSymbolList
     */
    public function testMatch_ValidText_CallsOnSymbolWithFirstSymbol(
        string $text,
        int $expectedFinishOffset,
        int $expectedSymbol
    ): void {
        $buffer = SymbolBuffer::fromString($text);
        $match = $this
            ->createMock(LexemeListenerInterface::class);
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, $expectedFinishOffset);
        $lexeme = new SymbolLexeme($lexemeInfo, $expectedSymbol);
        $match
            ->expects($this->once())
            ->method('onSymbol')
            ->with($this->equalTo($lexeme));
        /** @var LexemeListenerInterface $match */
        (new Utf8LexemeMatcher)->match($buffer, $match);
    }

    public function providerValidSymbolList(): array
    {
        return [
            'Single ASCII char' => ['a', 1, 0x61],
            'Multiple ASCII chars' => ['cba', 1, 0x63],
            'Single cyrillic char' => ['б', 2, 0x0431],
            'Single Japanese hieroglyph' => ['本', 3, 0x672C],
            'Single cuneiform char' => ["\u{0122F0}", 4, 0x122F0],
            'Single 5-byte NULL char' => ["\xF8\x80\x80\x80\x80", 5, 0x00],
            'Single 6-byte NULL char' => ["\xFC\x80\x80\x80\x80\x80", 6, 0x00],
        ];
    }

    /**
     * @param string $text
     * @param int $expectedFinishOffset
     * @dataProvider providerInvalidFirstByteList
     */
    public function testMatch_InvalidFirstByteText_CallsOnInvalidByteWithFirstByte(
        string $text,
        int $expectedFinishOffset
    ): void {
        $buffer = SymbolBuffer::fromString($text);
        /** @var MockObject $match */
        $match = $this
            ->createMock(LexemeListenerInterface::class);
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, $expectedFinishOffset);
        $lexeme = new InvalidBytesLexeme($lexemeInfo);
        $match
            ->expects($this->once())
            ->method('onInvalidBytes')
            ->with($this->equalTo($lexeme));
        /** @var LexemeListenerInterface $match */
        (new Utf8LexemeMatcher)->match($buffer, $match);
    }

    public function providerInvalidFirstByteList(): array
    {
        return [
            'Single tail byte' => ["\x80", 1],
            'Tail byte before valid ASCII symbol' => ["\x80a", 1],
            'Null byte as 2-byte symbol tail' => ["\xC0\x00", 2],
            'Null byte as 3-byte symbol last tail' => ["\xE0\xAF\x00", 3],
            'Null byte as 3-byte symbol first tail' => ["\xE0\x00\xB5", 2],
            'Null byte as 4-byte symbol first tail' => ["\xF0\x00\x88\x98", 2],
            'Null byte as 5-byte symbol first tail' => ["\xF8\x00\x80\x90\x9A", 2],
            'Null byte as 6-byte symbol first tail' => ["\xFC\x00\x80\x80\x90\x9A", 2],
            'Null byte as 6-byte symbol last tail' => ["\xFC\x80\x80\x80\x90\x00", 6],
        ];
    }
}
