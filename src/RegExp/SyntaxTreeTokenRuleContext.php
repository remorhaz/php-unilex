<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;

class SyntaxTreeTokenRuleContext
{

    private $symbol;

    private $token;

    public function __construct(ParsedSymbol $symbol, ParsedToken $token)
    {
        $this->symbol = $symbol;
        $this->token = $token;
    }

    public function getSymbol(): ParsedSymbol
    {
        return $this->symbol;
    }

    public function getToken(): ParsedToken
    {
        return $this->token;
    }

    /**
     * @param string $target
     * @param string|null $source
     * @return SyntaxTreeTokenRuleContext
     * @throws \Remorhaz\UniLex\Exception
     */
    public function setTokenAttribute(string $target, string $source = null): self
    {
        $value = $this
            ->getToken()
            ->getToken()
            ->getAttribute($source ?? $target);
        $this
            ->getSymbol()
            ->setAttribute($target, $value);
        return $this;
    }
}
