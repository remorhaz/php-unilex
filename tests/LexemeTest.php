<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LexemeInfoInterface;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\Lexeme;

class LexemeTest extends TestCase
{

    public function testGetInfo_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $expectedValue = new SymbolBufferLexemeInfo($buffer, 0 ,1);
        $lexeme = $this->createLexeme($expectedValue);
        $actualValue = $lexeme->getInfo();
        self::assertSame($expectedValue, $actualValue);
    }

    private function createLexeme(LexemeInfoInterface $lexemeInfo): Lexeme
    {
        return new class($lexemeInfo) extends Lexeme
        {
        };
    }
}
