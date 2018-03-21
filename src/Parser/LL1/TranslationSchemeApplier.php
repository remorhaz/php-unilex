<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
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
     * @param ParsedProduction $production
     */
    public function onSymbol(int $symbolIndex, ParsedProduction $production): void
    {
        //echo "Symbol {$production}[{$symbolIndex}]->{$production->getSymbol($symbolIndex)->getSymbolId()}", PHP_EOL;
        $this
            ->scheme
            ->applySymbolActions($production, $symbolIndex);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param Token $token
     */
    public function onToken(ParsedSymbol $symbol, Token $token): void
    {
        //echo "Token {$symbol->getSymbolId()} -> {$token->getToken()->getType()}", PHP_EOL;
        $this
            ->scheme
            ->applyTokenActions($symbol, $token);
    }

    /**
     * @param ParsedProduction $production
     */
    public function onFinishProduction(ParsedProduction $production): void
    {
        //echo "Finish {$production}", PHP_EOL;
        $this
            ->scheme
            ->applyProductionActions($production);
    }

    public function onBeginProduction(ParsedProduction $production): void
    {
        //echo "Begin {$production}", PHP_EOL;
    }

    public function onRootSymbol(ParsedSymbol $symbol): void
    {
        //echo "Root symbol {$symbol->getSymbolId()}", PHP_EOL;
    }
}
