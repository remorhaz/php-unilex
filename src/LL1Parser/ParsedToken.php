<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Token;

class ParsedToken extends ParsedNode
{

    private $token;

    public function __construct(int $index, Token $token)
    {
        parent::__construct($index);
        $this->token = $token;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
