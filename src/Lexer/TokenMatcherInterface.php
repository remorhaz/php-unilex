<?php

namespace Remorhaz\UniLex\Lexer;

use Remorhaz\UniLex\IO\CharBufferInterface;

interface TokenMatcherInterface
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool;

    public function getToken(): Token;
}
