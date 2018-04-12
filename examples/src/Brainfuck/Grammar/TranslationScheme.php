<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Token;

class TranslationScheme implements TranslationSchemeInterface
{

    private $productionScheme;

    private $symbolScheme;

    private $tokenScheme;

    public function __construct()
    {
        $this->productionScheme = new ProductionTranslationScheme();
        $this->symbolScheme = new SymbolTranslationScheme();
        $this->tokenScheme = new TokenTranslationScheme();
    }

    public function applyProductionActions(Production $production): void
    {
        $this
            ->productionScheme
            ->applyActions($production);
    }

    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
        $this
            ->symbolScheme
            ->applyActions($production, $symbolIndex);
    }

    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        $this
            ->tokenScheme
            ->applyActions($symbol, $token);
    }
}
