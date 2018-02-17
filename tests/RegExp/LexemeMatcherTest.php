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

    public function testMatch_ValidBuffer_CallsOnTokenWithFirstSymbolLexeme()
    {
        $symbolBuffer = SymbolBuffer::fromString('a');
        $matcher = new LexemeMatcher;
        $match = $this
            ->createMock(LexemeListenerInterface::class);
        $lexemeInfo = new SymbolBufferLexemeInfo($symbolBuffer, 0, 1);
        $lexeme = new Lexeme($lexemeInfo, TokenType::OTHER_HEX_LETTER, 0x61);
        $match
            ->expects($this->once())
            ->method('onToken')
            ->with($this->equalTo($lexeme));

        /** @var LexemeListenerInterface $match */
        $matcher->match($symbolBuffer, $match);
    }

    public function testMatch_InalidBuffer_CallsOnInvalidTokenWithFirstSymbolLexeme()
    {
        $symbolBuffer = SymbolBuffer::fromArray([0x110000]);
        $matcher = new LexemeMatcher;
        $match = $this
            ->createMock(LexemeListenerInterface::class);
        $lexemeInfo = new SymbolBufferLexemeInfo($symbolBuffer, 0, 1);
        $lexeme = new Lexeme($lexemeInfo, TokenType::INVALID, 0x110000);
        $match
            ->expects($this->once())
            ->method('onInvalidToken')
            ->with($this->equalTo($lexeme));

        /** @var LexemeListenerInterface $match */
        $matcher->match($symbolBuffer, $match);
    }
}
