<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

class TokenFactory implements TokenFactoryInterface
{
    private $eoiTokenId;

    public function __construct(int $eoiTokenId)
    {
        $this->eoiTokenId = $eoiTokenId;
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
