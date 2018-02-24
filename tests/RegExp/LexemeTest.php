<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\SymbolLexeme;
use Remorhaz\UniLex\RegExp\TokenType;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class LexemeTest extends TestCase
{

    public function testGetType_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $lexeme = new SymbolLexeme($lexemeInfo, TokenType::OTHER_HEX_LETTER, 0x61);
        $actual = $lexeme->getType();
        self::assertSame(TokenType::OTHER_HEX_LETTER, $actual);
    }

    public function testGetSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $lexeme = new SymbolLexeme($lexemeInfo, TokenType::OTHER_HEX_LETTER, 0x61);
        $actual = $lexeme->getSymbol();
        self::assertSame(0x61, $actual);
    }
}
