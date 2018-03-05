<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\SymbolFactoryInterface;

class CodeSymbolFactory implements SymbolFactoryInterface
{

    /**
     * @param Token $token
     * @return int
     * @throws Exception
     */
    public function getSymbol(Token $token): int
    {
        return $this->getSymbolInfo($token)->getCode();
    }

    /**
     * @param Token $token
     * @return SymbolInfo
     * @throws Exception
     */
    private function getSymbolInfo(Token $token): SymbolInfo
    {
        $symbolInfo = $token->getMatcherInfo();
        if (!isset($symbolInfo)) {
            throw new Exception("No matcher info attached to token {$token->getType()}");
        }
        if ($symbolInfo instanceof SymbolInfo) {
            return $symbolInfo;
        }
        throw new Exception("Invalid matcher info attached to token {$token->getType()}");
    }
}
