<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeFactoryInterface;

class LexemeFactory implements LexemeFactoryInterface
{

    public function createEoiLexeme(): Lexeme
    {
        return $this->createLexeme(TokenType::EOI);
    }

    public function createLexeme(int $tokenId): Lexeme
    {
        return new Lexeme($tokenId, TokenType::EOI == $tokenId);
    }
}
