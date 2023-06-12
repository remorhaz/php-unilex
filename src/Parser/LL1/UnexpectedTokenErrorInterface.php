<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Symbol;

interface UnexpectedTokenErrorInterface
{
    public function getUnexpectedToken(): Token;

    public function getProductionHeader(): Symbol;

    /**
     * @return list<int>
     */
    public function getExpectedTokenTypeList(): array;
}
