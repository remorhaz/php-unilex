<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\LL1Parser\SDD\RuleSetApplier;

class SyntaxTreeBuilder extends RuleSetApplier
{

    private $tree;

    public function __construct()
    {
        $tree = new SyntaxTree;
        parent::__construct(new SyntaxTreeRuleSet($tree));
        $this->tree = $tree;
    }

    public function getTree(): SyntaxTree
    {
        return $this->tree;
    }
}