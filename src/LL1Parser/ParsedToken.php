<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Token;

class ParsedToken
{

    private $index;

    private $token;

    public function __construct(int $index, Token $token)
    {
        $this->index = $index;
        $this->token = $token;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
