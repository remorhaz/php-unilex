<?php

namespace Remorhaz\UniLex;

interface TokenMatcherInterface
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool;

    public function getToken(): Token;
}
