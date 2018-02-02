<?php

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\Unicode\SymbolLexeme;

class SymbolLexemeTest extends TestCase
{

    public function testGetSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $lexemeInfo = new SymbolBufferLexemeInfo($buffer, 0, 1);
        $expectedValue = 0x00000061;
        $lexeme = new SymbolLexeme($lexemeInfo, $expectedValue);
        $actualValue = $lexeme->getSymbol();
        self::assertSame($expectedValue, $actualValue);
    }
}
