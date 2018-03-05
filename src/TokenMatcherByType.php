<?php

namespace Remorhaz\UniLex;

class TokenMatcherByType implements TokenMatcherInterface
{

    public function match(SymbolBufferInterface $buffer, TokenFactoryInterface $tokenFactory): Token
    {
        $tokenId = $buffer->getSymbol();
        $token = $tokenFactory->createToken($tokenId);
        $buffer->nextSymbol();
        return $token;
    }
}
