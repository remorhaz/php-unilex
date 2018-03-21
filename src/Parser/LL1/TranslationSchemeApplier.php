<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Token;

class TranslationSchemeApplier extends AbstractParserListener
{

    private $scheme;

    public function __construct(TranslationSchemeInterface $scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * @param int $symbolIndex
     * @param Production $production
     */
    public function onSymbol(int $symbolIndex, Production $production): void
    {
        //echo "Symbol {$production}[{$symbolIndex}]->{$production->getSymbol($symbolIndex)->getSymbolId()}", PHP_EOL;
        $this
            ->scheme
            ->applySymbolActions($production, $symbolIndex);
    }

    /**
     * @param Symbol $symbol
     * @param Token $token
     */
    public function onToken(Symbol $symbol, Token $token): void
    {
        //echo "Token {$symbol->getSymbolId()} -> {$token->getToken()->getType()}", PHP_EOL;
        $this
            ->scheme
            ->applyTokenActions($symbol, $token);
    }

    /**
     * @param Production $production
     */
    public function onFinishProduction(Production $production): void
    {
        //echo "Finish {$production}", PHP_EOL;
        $this
            ->scheme
            ->applyProductionActions($production);
    }

    public function onBeginProduction(Production $production): void
    {
        //echo "Begin {$production}", PHP_EOL;
    }

    public function onRootSymbol(Symbol $symbol): void
    {
        //echo "Root symbol {$symbol->getSymbolId()}", PHP_EOL;
    }
}
