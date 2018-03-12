<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;
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
     * @throws Exception
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

    /**
     * @param int $index
     * @param string $target
     * @param string|null $source
     * @return SyntaxTreeProductionRuleContext
     * @throws Exception
     */
    public function copySymbolAttribute(int $index, string $target, string $source = null): self
    {
        $value = $this->getSymbolAttribute($index, $source ?? $target);
        return $this
            ->setHeaderAttribute($target, $value);
    }

    /**
     * @param int $index
     * @param string $attr
     * @return mixed
     * @throws Exception
     */
    public function getSymbolAttribute(int $index, string $attr)
    {
        return $this
            ->getProduction()
            ->getSymbol($index)
            ->getAttribute($attr);
    }

    /**
     * @param string $attr
     * @return mixed
     * @throws Exception
     */
    public function getHeaderAttribute(string $attr)
    {
        return $this
            ->getProduction()
            ->getHeader()
            ->getAttribute($attr);
    }

    /**
     * @param string $attr
     * @param $value
     * @return SyntaxTreeProductionRuleContext
     * @throws Exception
     */
    public function setHeaderAttribute(string $attr, $value): self
    {
        $this
            ->getProduction()
            ->getHeader()
            ->setAttribute($attr, $value);
        return $this;
    }
}
