<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\SymbolFactoryInterface;

class CodeSymbolFactory implements SymbolFactoryInterface
{

    /**
     * @param Lexeme $lexeme
     * @return int
     * @throws Exception
     */
    public function getSymbol(Lexeme $lexeme): int
    {
        return $this->getSymbolInfo($lexeme)->getCode();
    }

    /**
     * @param Lexeme $lexeme
     * @return SymbolInfo
     * @throws Exception
     */
    private function getSymbolInfo(Lexeme $lexeme): SymbolInfo
    {
        $symbolInfo = $lexeme->getMatcherInfo();
        if (!isset($symbolInfo)) {
            throw new Exception("No matcher info attached to lexeme {$lexeme->getType()}");
        }
        if ($symbolInfo instanceof SymbolInfo) {
            return $symbolInfo;
        }
        throw new Exception("Invalid matcher info attached to lexeme {$lexeme->getType()}");
    }
}
