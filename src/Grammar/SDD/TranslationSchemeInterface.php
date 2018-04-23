<?php

namespace Remorhaz\UniLex\Grammar\SDD;

use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Lexer\Token;

interface TranslationSchemeInterface
{

    /**
     * @param Production $production
     * @param int $symbolIndex
     */
    public function applySymbolActions(Production $production, int $symbolIndex): void;

    /**
     * @param Symbol $symbol
     * @param Token $token
     */
    public function applyTokenActions(Symbol $symbol, Token $token): void;

    /**
     * @param Production $production
     */
    public function applyProductionActions(Production $production): void;
}
