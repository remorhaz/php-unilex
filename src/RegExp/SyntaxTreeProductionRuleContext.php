<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\SDD\ProductionContextInterface;

class SyntaxTreeProductionRuleContext implements ProductionContextInterface
{

    private $tree;

    private $production;

    public function __construct(SyntaxTree $tree, ParsedProduction $production)
    {
        $this->tree = $tree;
        $this->production = $production;
    }

    public function getTree(): SyntaxTree
    {
        return $this->tree;
    }

    public function getProduction(): ParsedProduction
    {
        return $this->production;
    }

    /**
     * @param string $attr
     * @return SyntaxTreeNode
     * @throws \Remorhaz\UniLex\Exception
     */
    public function getNode(string $attr): SyntaxTreeNode
    {
        $nodeId = $this
            ->getProduction()
            ->getHeader()
            ->getAttribute($attr);
        return $this
            ->getTree()
            ->getNode($nodeId);
    }

}
