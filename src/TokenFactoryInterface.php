<?php

namespace Remorhaz\UniLex;

interface TokenFactoryInterface
{

    public function createToken(int $tokenId): Token;

    public function createEoiToken(): Token;
}