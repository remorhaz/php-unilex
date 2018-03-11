<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;
use Remorhaz\UniLex\LL1Parser\SDD\AbstractRuleSet;

class SyntaxTreeRuleSet extends AbstractRuleSet
{

    private $tree;

    public function __construct(SyntaxTree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @return callable
     */
    protected function getSymbolRule(ParsedProduction $production, int $symbolIndex): callable
    {
        return function (ParsedProduction $production, int $symbolIndex) {
            $context = new SyntaxTreeSymbolRuleContext($this->tree, $production, $symbolIndex);
            parent::getSymbolRule($production, $symbolIndex)($context);
        };
    }

    protected function getTokenRule(ParsedSymbol $symbol): callable
    {
        return function (ParsedSymbol $symbol, ParsedToken $token) {
            $context = new SyntaxTreeTokenRuleContext($symbol, $token);
            parent::getTokenRule($symbol)($context);
        };
    }
}
