<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParseTreeTokenNode implements ParseTreeNodeInterface
{

    private $token;

    public function __construct(ParsedToken $token)
    {
        $this->token = $token;
    }

    public function getToken(): ParsedToken
    {
        return $this->token;
    }

    public function getIndex(): int
    {
        return $this->token->getIndex();
    }
}