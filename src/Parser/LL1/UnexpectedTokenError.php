<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Symbol;

class UnexpectedTokenError implements UnexpectedTokenErrorInterface
{

    private $unexpectedToken;

    private $productionHeader;

    private $expectedTokenTypeList;

    public function __construct(Token $unexpectedToken, Symbol $productionHeader, int ...$expectedTokenTypeList)
    {
        $this->unexpectedToken = $unexpectedToken;
        $this->productionHeader = $productionHeader;
        $this->expectedTokenTypeList = $expectedTokenTypeList;
    }

    public function getUnexpectedToken(): Token
    {
        return $this->unexpectedToken;
    }

    public function getExpectedTokenTypeList(): array
    {
        return $this->expectedTokenTypeList;
    }

    public function getProductionHeader(): Symbol
    {
        return $this->productionHeader;
    }
}
