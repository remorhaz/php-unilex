<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenFactoryInterface;
use Remorhaz\UniLex\TokenMatcherInterface;

abstract class Utf8TokenMatcherTemplate implements TokenMatcherInterface
{

    private $token;

    private $char;

    private $charList = [];

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
