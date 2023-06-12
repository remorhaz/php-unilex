<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Lexer\Token;

class TranslationSchemeApplier extends AbstractParserListener
{
    public function __construct(
        private TranslationSchemeInterface $scheme,
    ) {
    }

    public function onSymbol(int $symbolIndex, Production $production): void
    {
        $this
            ->scheme
            ->applySymbolActions($production, $symbolIndex);
    }

    public function onToken(Symbol $symbol, Token $token): void
    {
        $this
            ->scheme
            ->applyTokenActions($symbol, $token);
    }

    public function onFinishProduction(Production $production): void
    {
        $this
            ->scheme
            ->applyProductionActions($production);
    }

    public function onBeginProduction(Production $production): void
    {
    }

    public function onRootSymbol(Symbol $symbol): void
    {
    }
}
