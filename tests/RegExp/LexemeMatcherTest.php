<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\Lexeme;
use Remorhaz\UniLex\RegExp\LexemeListenerInterface;
use Remorhaz\UniLex\RegExp\LexemeMatcher;
use Remorhaz\UniLex\RegExp\TokenType;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class LexemeMatcherTest extends TestCase
{

    /**
     * @param int $type
     * @param int $symbol
     * @dataProvider providerValidLexeme
     */
    public function testMatch_ValidBuffer_CallsOnTokenWithFirstSymbolLexeme(int $type, int $symbol): void
    {
        $buffer = SymbolBuffer::fromSymbols($symbol);
        $matcher = new LexemeMatcher;
        $match = $this
            ->createMock(LexemeListenerInterface::class);
        $info = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $expectedLexeme = new Lexeme($info, $type, $symbol);
        $match
            ->expects($this->once())
            ->method('onToken')
            ->with($this->equalTo($expectedLexeme));

        /** @var LexemeListenerInterface $match */
        $matcher->match($buffer, $match);
    }

    public function providerValidLexeme(): array
    {
        return [
            [TokenType::CTL_ASCII, ord("\t")],
            [TokenType::DOLLAR, ord('$')],
            [TokenType::LEFT_BRACKET, ord('(')],
            [TokenType::RIGHT_BRACKET, ord(')')],
            [TokenType::STAR, ord('*')],
            [TokenType::PLUS, ord('+')],
            [TokenType::COMMA, ord(',')],
            [TokenType::HYPHEN, ord('-')],
            [TokenType::DOT, ord('.')],
            [TokenType::DIGIT_ZERO, ord('0')],
            [TokenType::DIGIT_OCT, ord('1')],
            [TokenType::DIGIT_DEC, ord('8')],
            [TokenType::QUESTION, ord('?')],
            [TokenType::CAPITAL_P, ord('P')],
            [TokenType::LEFT_SQUARE_BRACKET, ord('[')],
            [TokenType::BACKSLASH, ord('\\')],
            [TokenType::RIGHT_SQUARE_BRACKET, ord(']')],
            [TokenType::CIRCUMFLEX, ord('^')],
            [TokenType::SMALL_C, ord('c')],
            [TokenType::SMALL_O, ord('o')],
            [TokenType::SMALL_P, ord('p')],
            [TokenType::SMALL_U, ord('u')],
            [TokenType::SMALL_X, ord('x')],
            [TokenType::LEFT_CURLY_BRACKET, ord('{')],
            [TokenType::VERTICAL_LINE, ord('|')],
            [TokenType::RIGHT_CURLY_BRACKET, ord('}')],
            [TokenType::OTHER_HEX_LETTER, ord('a')],
            [TokenType::OTHER_HEX_LETTER, ord('d')],
            [TokenType::OTHER_HEX_LETTER, ord('A')],
            [TokenType::OTHER_ASCII_LETTER, ord('G')],
            [TokenType::OTHER_ASCII_LETTER, ord('Q')],
            [TokenType::OTHER_ASCII_LETTER, ord('g')],
            [TokenType::OTHER_ASCII_LETTER, ord('q')],
            [TokenType::OTHER_ASCII_LETTER, ord('v')],
            [TokenType::OTHER_ASCII_LETTER, ord('y')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord('!')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord('%')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord('/')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord(':')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord('@')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord('_')],
            [TokenType::PRINTABLE_ASCII_OTHER, ord('~')],
            [TokenType::OTHER_ASCII, ord("\x7F")],
            [TokenType::NOT_ASCII, 0x0411],
        ];
    }

    public function testMatch_InalidBuffer_CallsOnInvalidTokenWithFirstSymbolLexeme(): void
    {
        $buffer = SymbolBuffer::fromSymbols(0x110000);
        $matcher = new LexemeMatcher;
        $match = $this
            ->createMock(LexemeListenerInterface::class);
        $info = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $lexeme = new Lexeme($info, TokenType::INVALID, 0x110000);
        $match
            ->expects($this->once())
            ->method('onInvalidToken')
            ->with($this->equalTo($lexeme));

        /** @var LexemeListenerInterface $match */
        $matcher->match($buffer, $match);
    }
}
