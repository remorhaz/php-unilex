<?php

namespace Remorhaz\UniLex\SyntaxTree\SDD;

use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;
use Remorhaz\UniLex\LL1Parser\SDD\TokenContextInterface;

class TokenRuleContext implements TokenContextInterface
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
     * @return TokenRuleContext
     * @throws \Remorhaz\UniLex\Exception
     */
    public function copyTokenAttribute(string $target, string $source = null): self
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
