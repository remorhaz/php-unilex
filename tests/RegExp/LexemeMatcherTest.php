<?php

namespace Remorhaz\UniLex\Test\RegExp;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\RegExp\Lexeme;
use Remorhaz\UniLex\RegExp\LexemeListenerInterface;
use Remorhaz\UniLex\RegExp\LexemeMatcher;
use Remorhaz\UniLex\RegExp\TokenType;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;
use Remorhaz\UniLex\Unicode\Scanner;
use Remorhaz\UniLex\Unicode\Utf8LexemeMatcher;

class LexemeMatcherTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testMatch_ValidText_CallsOnTokenWithFirstSymbolLexeme()
    {
        $byteBuffer = SymbolBuffer::fromString("a");
        $scanner = new Scanner($byteBuffer, new Utf8LexemeMatcher);
        $symbol = $scanner->read();
        $symbolBuffer = new SymbolBuffer($symbol->getInfo()->extract());
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
}
