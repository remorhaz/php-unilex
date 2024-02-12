<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Symbol;

use function array_values;

class UnexpectedTokenError implements UnexpectedTokenErrorInterface
{
    /**
     * @var list<int>
     */
    private array $expectedTokenTypeList;

    public function __construct(
        private readonly Token $unexpectedToken,
        private readonly Symbol $productionHeader,
        int ...$expectedTokenTypeList,
    ) {
        $this->expectedTokenTypeList = array_values($expectedTokenTypeList);
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
