<?php

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Utf8TokenMatcher;

/**
 * @covers \Remorhaz\UniLex\Unicode\Utf8TokenMatcher
 */
class Utf8TokenMatcherTest extends TestCase
{

    /**
     * @param string $text
     * @dataProvider providerValidSymbolList
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_ValidText_ReturnsSymbolToken(string $text): void
    {
        $buffer = CharBuffer::fromString($text);
        $actual = (new Utf8TokenMatcher)->match($buffer, new TokenFactory)->getType();
        self::assertEquals(TokenType::SYMBOL, $actual);
    }

    /**
     * @param string $text
     * @param int $expectedSymbol
     * @dataProvider providerValidSymbolList
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_ValidText_ReturnsTokenWithMatchingSymbolAttribute(
        string $text,
        int $expectedSymbol
    ): void {
        $buffer = CharBuffer::fromString($text);
        $actualValue = (new Utf8TokenMatcher)->match($buffer, new TokenFactory)
            ->getAttribute(TokenAttribute::SYMBOL);
        self::assertEquals($expectedSymbol, $actualValue);
    }

    public function providerValidSymbolList(): array
    {
        return [
            'Single ASCII char' => ['a', 0x61],
            'Multiple ASCII chars' => ['cba', 0x63],
            'Single cyrillic char' => ['б', 0x0431],
            'Single Japanese hieroglyph' => ['本', 0x672C],
            'Single cuneiform char' => ["\u{0122F0}", 0x122F0],
            'Single 5-byte NULL char' => ["\xF8\x80\x80\x80\x80", 0x00],
            'Single 6-byte NULL char' => ["\xFC\x80\x80\x80\x80\x80", 0x00],
        ];
    }

    /**
     * @param string $text
     * @dataProvider providerInvalidText
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_InvalidText_ReturnsInvalidBytesToken(string $text): void
    {
        $buffer = CharBuffer::fromString($text);
        $actual = (new Utf8TokenMatcher)->match($buffer, new TokenFactory)->getType();
        self::assertEquals(TokenType::INVALID_BYTES, $actual);
    }

    public function providerInvalidText(): array
    {
        return [
            'Single tail byte' => ["\x80", 1, 0x80],
            'Tail byte before valid ASCII symbol' => ["\x80a", 1, 0x80],
            'Null byte as 2-byte symbol tail' => ["\xC0\x00", 2, 0x00],
            'Null byte as 3-byte symbol last tail' => ["\xE0\xAF\x00", 3, 0x00],
            'Null byte as 3-byte symbol first tail' => ["\xE0\x00\xB5", 2, 0x00],
            'Null byte as 4-byte symbol first tail' => ["\xF0\x00\x88\x98", 2, 0x00],
            'Null byte as 5-byte symbol first tail' => ["\xF8\x00\x80\x90\x9A", 2, 0x00],
            'Null byte as 6-byte symbol first tail' => ["\xFC\x00\x80\x80\x90\x9A", 2, 0x00],
            'Null byte as 6-byte symbol last tail' => ["\xFC\x80\x80\x80\x90\x00", 6, 0x00],
        ];
    }
}
