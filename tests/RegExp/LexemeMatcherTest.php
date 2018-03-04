<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\LexemeFactory;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\LexemeMatcher;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\Unicode\SymbolInfo;

/**
 * @covers \Remorhaz\UniLex\RegExp\LexemeMatcher
 */
class LexemeMatcherTest extends TestCase
{

    /**
     * @param int $expectedType
     * @param int $symbol
     * @dataProvider providerValidLexemeType
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_ValidBuffer_ReturnsLexemeWithMatchingType(int $expectedType, int $symbol): void
    {
        $buffer = SymbolBuffer::fromSymbols($symbol);
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $lexemeFactory = new LexemeFactory($grammar);
        $actualValue = (new LexemeMatcher)->match($buffer, $lexemeFactory)->getType();
        self::assertEquals($expectedType, $actualValue);
    }

    public function providerValidLexemeType(): array
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

    /**
     * @param int $symbol
     * @dataProvider providerValidLexemeSymbol
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_ValidBuffer_ReturnsLexemeWithMatchingSymbolInfo(int $symbol): void
    {
        $buffer = SymbolBuffer::fromSymbols($symbol);
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $lexemeFactory = new LexemeFactory($grammar);
        $actualValue = (new LexemeMatcher)->match($buffer, $lexemeFactory)->getMatcherInfo();
        $expectedValue = new SymbolInfo($symbol);
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidLexemeSymbol(): array
    {
        $data = [];
        foreach ($this->providerValidLexemeType() as $key => $dataSet) {
            $data[$key] = [$dataSet[1]];
        }
        return $data;
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_InvalidBuffer_ReturnsInvalidLexeme(): void
    {
        $buffer = SymbolBuffer::fromSymbols(0x110000);
        $matcher = new LexemeMatcher;
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $lexemeFactory = new LexemeFactory($grammar);
        $actualValue = $matcher->match($buffer, $lexemeFactory)->getType();
        self::assertEquals(TokenType::INVALID, $actualValue);
    }
}
