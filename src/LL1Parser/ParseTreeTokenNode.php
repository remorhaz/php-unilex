<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Token;

class ParseTreeTokenNode implements ParseTreeNodeInterface
{

    private $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}