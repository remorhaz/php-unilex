<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Token;

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
        $this->tokenScheme = new TokenTranslationScheme;
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @throws Exception
     */
    public function applySymbolActions(ParsedProduction $production, int $symbolIndex): void
    {
        $this
            ->symbolScheme
            ->applyActions($production, $symbolIndex);
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function applyProductionActions(ParsedProduction $production): void
    {
        $this
            ->productionScheme
            ->applyActions($production);
    }

    /**
     * @param ParsedSymbol $symbol
     * @param Token $token
     * @throws Exception
     */
    public function applyTokenActions(ParsedSymbol $symbol, Token $token): void
    {
        $this
            ->tokenScheme
            ->applyActions($symbol, $token);
    }
}
