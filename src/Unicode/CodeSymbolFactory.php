<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\SymbolFactoryInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

class CodeSymbolFactory implements SymbolFactoryInterface
{

    public function __construct()
    {
    }

    /**
     * @param Token $token
     * @return int
     * @throws Exception
     */
    public function getSymbol(Token $token): int
    {
        return $token->getAttribute(TokenAttribute::SYMBOL);
    }
}
