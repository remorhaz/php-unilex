<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;

class TokenFactory implements TokenFactoryInterface
{
    public function createEoiToken(): Token
    {
        return $this->createToken(TokenType::EOI);
    }

    public function createToken(int $tokenId): Token
    {
        return new Token($tokenId, TokenType::EOI == $tokenId);
    }
}
