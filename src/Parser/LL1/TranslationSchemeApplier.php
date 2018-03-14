<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\SDD\TranslationScheme;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

class TranslationSchemeApplier extends AbstractParserListener
{

    private $scheme;

    public function __construct(TranslationScheme $scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * @param int $symbolIndex
     * @param ParsedProduction $production
     * @throws Exception
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
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
        //echo "Token {$symbol->getSymbolId()} -> {$token->getToken()->getType()}", PHP_EOL;
        $this
            ->scheme
            ->applyTokenActions($symbol, $token);
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
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
