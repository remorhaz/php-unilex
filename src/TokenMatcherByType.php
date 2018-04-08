<?php

namespace Remorhaz\UniLex;

class TokenMatcherByType implements TokenMatcherInterface
{

    private $token;

    /**
     * @return Token
     * @throws Exception
     */
    public function getToken(): Token
    {
        if (!isset($this->token)) {
            throw new Exception("Token is not defined");
        }
        return $this->token;
    }

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        unset($this->token);
        $tokenId = $buffer->getSymbol();
        $this->token = $tokenFactory->createToken($tokenId);
        $buffer->nextSymbol();
        return true;
    }
}
