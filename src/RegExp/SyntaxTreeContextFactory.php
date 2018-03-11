<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;
use Remorhaz\UniLex\LL1Parser\SDD\ContextFactoryInterface;
use Remorhaz\UniLex\LL1Parser\SDD\ProductionContextInterface;
use Remorhaz\UniLex\LL1Parser\SDD\SymbolContextInterface;
use Remorhaz\UniLex\LL1Parser\SDD\TokenContextInterface;

class SyntaxTreeContextFactory implements ContextFactoryInterface
{

    private $tree;

    public function __construct(SyntaxTree $tree)
    {
        $this->tree = $tree;
    }

    public function createSymbolContext(ParsedProduction $production, int $symbolIndex): SymbolContextInterface
    {
        return new SyntaxTreeSymbolRuleContext($this->tree, $production, $symbolIndex);
    }

    public function createProductionContext(ParsedProduction $production): ProductionContextInterface
    {
        return new SyntaxTreeProductionRuleContext($this->tree, $production);
    }

    public function createTokenContext(ParsedSymbol $symbol, ParsedToken $token): TokenContextInterface
    {
        return new SyntaxTreeTokenRuleContext($symbol, $token);
    }
}
