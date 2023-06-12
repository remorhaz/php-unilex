<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

class TokenFactory implements TokenFactoryInterface
{
    public function __construct(
        private int $eoiTokenId,
    ) {
    }

    public function createToken(int $tokenId): Token
    {
        return new Token($tokenId, $tokenId == $this->eoiTokenId);
    }

    public function createEoiToken(): Token
    {
        return $this->createToken($this->eoiTokenId);
    }
}
