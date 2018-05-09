<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Lexer\Token;
use Remorhaz\UniLex\Parser\Symbol;

interface UnexpectedTokenErrorInterface
{

    public function getUnexpectedToken(): Token;

    public function getProductionHeader(): Symbol;

    public function getExpectedTokenTypeList(): array;
}
