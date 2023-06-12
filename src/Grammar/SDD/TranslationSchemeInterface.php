<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Grammar\SDD;

use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\Lexer\Token;

interface TranslationSchemeInterface
{
    public function applySymbolActions(Production $production, int $symbolIndex): void;

    public function applyTokenActions(Symbol $symbol, Token $token): void;

    public function applyProductionActions(Production $production): void;
}
