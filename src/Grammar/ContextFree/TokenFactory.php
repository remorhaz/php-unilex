<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;

class TokenFactory implements TokenFactoryInterface
{
    private $grammar;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    public function createToken(int $tokenId): Token
    {
        return new Token($tokenId, $this->isEoi($tokenId));
    }

    public function createEoiToken(): Token
    {
        return $this->createToken($this->grammar->getEoiToken());
    }

    protected function isEoi(int $tokenId): bool
    {
        return $this->grammar->isEoiToken($tokenId);
    }
}
