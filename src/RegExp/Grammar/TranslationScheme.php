<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\Production;
use Remorhaz\UniLex\Parser\Symbol;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Lexer\Token;

class TranslationScheme implements TranslationSchemeInterface
{

    private $tree;

    private $symbolScheme;

    private $productionScheme;

    private $tokenScheme;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
        $this->symbolScheme = new SymbolTranslationScheme($tree);
        $this->productionScheme = new ProductionTranslationScheme($tree);
        $this->tokenScheme = new TokenTranslationScheme();
    }

    /**
     * @param Production $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applySymbolActions(Production $production, int $symbolIndex): void
    {
        $this
            ->symbolScheme
            ->applyActions($production, $symbolIndex);
    }

    /**
     * @param Production $production
     * @throws Exception
     */
    public function applyProductionActions(Production $production): void
    {
        $this
            ->productionScheme
            ->applyActions($production);
    }

    /**
     * @param Symbol $symbol
     * @param Token $token
     * @throws Exception
     */
    public function applyTokenActions(Symbol $symbol, Token $token): void
    {
        $this
            ->tokenScheme
            ->applyActions($symbol, $token);
    }
}
