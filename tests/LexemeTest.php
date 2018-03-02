<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\LexemeInfoInterface;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\Lexeme;

/**
 * @covers \Remorhaz\UniLex\Lexeme
 */
class LexemeTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetInfo_ConstructWithValue_ReturnsSameValue(): void
    {
        $buffer = SymbolBuffer::fromString('a');
        $expectedValue = new SymbolBufferLexemeInfo($buffer, new LexemePosition(0 ,1));
        $lexeme = $this->createLexeme($expectedValue);
        $actualValue = $lexeme->getInfo();
        self::assertSame($expectedValue, $actualValue);
    }

    private function createLexeme(LexemeInfoInterface $lexemeInfo): Lexeme
    {
        return new class($lexemeInfo, 0) extends Lexeme
        {
        };
    }
}
