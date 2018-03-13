<?php

namespace Remorhaz\UniLex\Parser\SyntaxTree\SDD;

use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;
use Remorhaz\UniLex\Parser\LL1\SDD\ContextFactoryInterface;
use Remorhaz\UniLex\Parser\LL1\SDD\ProductionContextInterface;
use Remorhaz\UniLex\Parser\LL1\SDD\SymbolContextInterface;
use Remorhaz\UniLex\Parser\LL1\SDD\TokenContextInterface;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;

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
