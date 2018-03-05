<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenFactoryInterface;

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
