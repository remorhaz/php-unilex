<?php

namespace Remorhaz\UniLex;

abstract class TokenMatcherTemplate implements TokenMatcherInterface
{

    protected $token;

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
}
