<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Symbol;

class UnexpectedTokenError implements UnexpectedTokenErrorInterface
{
    /**
     * @var list<int>
     */
    private array $expectedTokenTypeList;

    public function __construct(
        private Token $unexpectedToken,
        private Symbol $productionHeader,
        int ...$expectedTokenTypeList,
    ) {
        $this->expectedTokenTypeList = $expectedTokenTypeList;
    }

    public function getUnexpectedToken(): Token
    {
        return $this->unexpectedToken;
    }

    /**
     * @return list<int>
     */
    public function getExpectedTokenTypeList(): array
    {
        return $this->expectedTokenTypeList;
    }

    public function getProductionHeader(): Symbol
    {
        return $this->productionHeader;
    }
}
