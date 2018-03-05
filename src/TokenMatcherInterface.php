<?php

namespace Remorhaz\UniLex;

interface TokenMatcherInterface
{

    public function match(SymbolBufferInterface $buffer, TokenFactoryInterface $tokenFactory): Token;
}
