<?php

namespace Remorhaz\UniLex\Grammar\SDD;

use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

interface TranslationSchemeInterface
{

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     */
    public function applySymbolActions(ParsedProduction $production, int $symbolIndex): void;

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     */
    public function applyTokenActions(ParsedSymbol $symbol, ParsedToken $token): void;

    /**
     * @param ParsedProduction $production
     */
    public function applyProductionActions(ParsedProduction $production): void;
}
