<?php

namespace Remorhaz\UniLex\SyntaxTree\SDD;

use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;
use Remorhaz\UniLex\LL1Parser\SDD\ContextFactoryInterface;
use Remorhaz\UniLex\LL1Parser\SDD\ProductionContextInterface;
use Remorhaz\UniLex\LL1Parser\SDD\SymbolContextInterface;
use Remorhaz\UniLex\LL1Parser\SDD\TokenContextInterface;
use Remorhaz\UniLex\SyntaxTree\Tree;

class ContextFactory implements ContextFactoryInterface
{

    private $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function createSymbolContext(ParsedProduction $production, int $symbolIndex): SymbolContextInterface
    {
        return new SymbolRuleContext($this->tree, $production, $symbolIndex);
    }

    public function createProductionContext(ParsedProduction $production): ProductionContextInterface
    {
        return new ProductionRuleContext($this->tree, $production);
    }

    public function createTokenContext(ParsedSymbol $symbol, ParsedToken $token): TokenContextInterface
    {
        return new TokenRuleContext($symbol, $token);
    }
}
