<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

interface TokenFactoryInterface
{
    public function createToken(int $tokenId): Token;

    public function createEoiToken(): Token;
}
