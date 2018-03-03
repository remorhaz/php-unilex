<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeFactoryInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

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
