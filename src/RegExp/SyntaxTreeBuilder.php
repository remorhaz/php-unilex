<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\AbstractParserListener;
use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

class SyntaxTreeBuilder extends AbstractParserListener
{

    /**
     * @param int $symbolIndex
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function onSymbol(int $symbolIndex, ParsedProduction $production): void
    {
        $symbol = $production->getSymbol($symbolIndex);
        switch ($symbol->getSymbolId()) {
            case SymbolType::NT_SYMBOL:
                if ($this->isProduction($production, SymbolType::NT_ITEM_BODY, 2)) {
                    $symbol->setAttribute('s.symbol_index', $symbol->getIndex());
                    // TODO: create symbol node #s.symbol_index
                }
                break;

            case SymbolType::NT_UNESC_SYMBOL:
                if ($this->isProduction($production, SymbolType::NT_SYMBOL, 2)) {
                    $symbol->setAttribute('l.symbol_index', $production->getHeader()->getAttribute('s.symbol_index'));
                }
                break;

            case SymbolType::T_OTHER_HEX_LETTER:
                if ($this->isProduction($production, SymbolType::NT_UNESC_SYMBOL, 14)) {
                    $symbolIndexAttribute = $production->getHeader()->getAttribute('l.symbol_index');
                    $symbol->setAttribute('l.symbol_index', $symbolIndexAttribute);
                    // TODO: set symbol node #l.symbol_index filter to [s.code]
                }
                break;
        }
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
        switch ($symbol->getSymbolId()) {
            case SymbolType::T_OTHER_HEX_LETTER:
                $code = $token->getToken()->getAttribute(TokenAttribute::UNICODE_CHAR);
                $symbol->setAttribute('s.code', $code);
                break;
        }
    }

    private function isProduction(ParsedProduction $production, int $symbolId, int $index): bool
    {
        return $production->getHeader()->getSymbolId() == $symbolId && $production->getIndex() == $index;
    }
}
