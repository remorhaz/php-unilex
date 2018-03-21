<?php

namespace Remorhaz\UniLex\Grammar\SDD;

use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Token;

interface TranslationSchemeInterface
{

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     */
    public function applySymbolActions(ParsedProduction $production, int $symbolIndex): void;

    /**
     * @param ParsedSymbol $symbol
     * @param Token $token
     */
    public function applyTokenActions(ParsedSymbol $symbol, Token $token): void;

    /**
     * @param ParsedProduction $production
     */
    public function applyProductionActions(ParsedProduction $production): void;
}
