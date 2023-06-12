<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Lexer\Token;

class TranslationScheme implements TranslationSchemeInterface
{
    private SymbolTranslationScheme $symbolScheme;

    private ProductionTranslationScheme $productionScheme;

    private TokenTranslationScheme $tokenScheme;

    public function __construct(Tree $tree)
    {
        $this->symbolScheme = new SymbolTranslationScheme($tree);
        $this->productionScheme = new ProductionTranslationScheme($tree);
        $this->tokenScheme = new TokenTranslationScheme();
    }

    /**
     * @throws Exception
     */
    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
        $this
            ->symbolScheme
            ->applyActions($production, $symbolIndex);
    }

    /**
     * @throws Exception
     */
    public function applyProductionActions(Production $production): void
    {
        $this
            ->productionScheme
            ->applyActions($production);
    }

    /**
     * @throws Exception
     */
    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        $this
            ->tokenScheme
            ->applyActions($symbol, $token);
    }
}
