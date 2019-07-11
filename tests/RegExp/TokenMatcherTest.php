<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\IO\CharBuffer;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;
use Remorhaz\UniLex\RegExp\Grammar\ConfigFile;
use Remorhaz\UniLex\RegExp\Grammar\TokenAttribute;
use Remorhaz\UniLex\RegExp\Grammar\TokenMatcher;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;

/**
 * @coversNothing
 */
class TokenMatcherTest extends TestCase
{

    /**
     * @param int $expectedType
     * @param int $symbol
     * @dataProvider providerValidTokenType
     * @throws UniLexException
     */
    public function testMatch_ValidBuffer_ReturnsTokenWithMatchingType(int $expectedType, int $symbol): void
    {
        $buffer = new CharBuffer($symbol);
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $matcher = new TokenMatcher;
        $matcher->match($buffer, new TokenFactory($grammar));
        $actualValue = $matcher->getToken()->getType();
        self::assertEquals($expectedType, $actualValue);
    }

    public function providerValidTokenType(): array
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
     * @dataProvider providerValidTokenSymbol
     * @throws UniLexException
     */
    public function testMatch_ValidBuffer_ReturnsTokenWithMatchingSymbolAttribute(int $symbol): void
    {
        $buffer = new CharBuffer($symbol);
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $matcher = new TokenMatcher;
        $matcher->match($buffer, new TokenFactory($grammar));
        $actualValue = $matcher->getToken()->getAttribute(TokenAttribute::CODE);
        self::assertEquals($symbol, $actualValue);
    }

    public function providerValidTokenSymbol(): array
    {
        $data = [];
        foreach ($this->providerValidTokenType() as $key => $dataSet) {
            $data[$key] = [$dataSet[1]];
        }
        return $data;
    }

    /**
     * @throws UniLexException
     */
    public function testMatch_InvalidBuffer_ReturnsInvalidToken(): void
    {
        $buffer = new CharBuffer(0x110000);
        $matcher = new TokenMatcher;
        $grammar = GrammarLoader::loadFile(ConfigFile::getPath());
        $matcher->match($buffer, new TokenFactory($grammar));
        $actualValue = $matcher->getToken()->getType();
        self::assertEquals(TokenType::INVALID, $actualValue);
    }
}
