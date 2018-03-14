<?php

namespace Remorhaz\UniLex\Parser\SyntaxTree\SDD;

use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;
use Remorhaz\UniLex\Grammar\SDD\TokenContextInterface;

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
     * @deprecated
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

    /**
     * @param string $name
     * @return mixed
     * @throws \Remorhaz\UniLex\Exception
     */
    public function getTokenAttribute(string $name)
    {
        return $this
            ->getToken()
            ->getToken()
            ->getAttribute($name);
    }
}
