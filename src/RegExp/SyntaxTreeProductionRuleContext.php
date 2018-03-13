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

    private function getTree(): SyntaxTree
    {
        return $this->tree;
    }

    private function getProduction(): ParsedProduction
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
        return $this
            ->getTree()
            ->getNode($this->getAttribute($attr));
    }

    /**
     * @param int $index
     * @param string $attr
     * @return SyntaxTreeNode
     * @throws Exception
     */
    public function getSymbolNode(int $index, string $attr): SyntaxTreeNode{
        return $this
            ->getTree()
            ->getNode($this->getSymbolAttribute($index, $attr));
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
            ->setAttribute($target, $value);
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
     * @param int $index
     * @param string[] ...$attrList
     * @return array
     * @throws Exception
     */
    public function getSymbolAttributeList(int $index, string ...$attrList): array
    {
        $valueList = [];
        foreach ($attrList as $attr) {
            $valueList[] = $this->getSymbolAttribute($index, $attr);
        }
        return $valueList;
    }

    /**
     * @param string $attr
     * @return mixed
     * @throws Exception
     */
    public function getAttribute(string $attr)
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
    public function setAttribute(string $attr, $value): self
    {
        $this
            ->getProduction()
            ->getHeader()
            ->setAttribute($attr, $value);
        return $this;
    }

    /**
     * @param string $target
     * @param string $source
     * @return SyntaxTreeProductionRuleContext
     * @throws Exception
     */
    public function copyAttribute(string $target, string $source): self
    {
        $this->setAttribute($target, $this->getAttribute($source));
        return $this;
    }

    /**
     * @param string $attr
     * @return SyntaxTreeProductionRuleContext
     * @throws Exception
     */
    public function setRootNode(string $attr): self
    {
        $this
            ->getTree()
            ->setRootNode($this->getNode($attr));
        return $this;
    }

    /**
     * @param string $name
     * @param string $attr
     * @return SyntaxTreeNode
     * @throws Exception
     */
    public function createNode(string $name, string $attr): SyntaxTreeNode
    {
        $node = $this
            ->getTree()
            ->createNode($name);
        $this->setAttribute($attr, $node->getId());
        return $node;
    }
}
