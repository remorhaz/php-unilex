<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\SDD\SymbolContextInterface;

class SyntaxTreeSymbolRuleContext implements SymbolContextInterface
{

    private $tree;

    private $production;

    private $symbolIndex;

    public function __construct(SyntaxTree $tree, ParsedProduction $production, int $symbolIndex)
    {
        $this->tree = $tree;
        $this->production = $production;
        $this->symbolIndex = $symbolIndex;
    }

    public function getTree(): SyntaxTree
    {
        return $this->tree;
    }

    public function getProduction(): ParsedProduction
    {
        return $this->production;
    }

    public function getSymbolIndex(): int
    {
        return $this->symbolIndex;
    }

    /**
     * @return ParsedSymbol
     * @throws Exception
     */
    public function getSymbol(): ParsedSymbol
    {
        return $this
            ->getProduction()
            ->getSymbol($this->getSymbolIndex());
    }

    /**
     * @param string $target
     * @param string|null $source
     * @return SyntaxTreeSymbolRuleContext
     * @throws Exception
     */
    public function inheritHeaderAttribute(string $target, string $source = null): self
    {
        $value = $this
            ->getProduction()
            ->getHeader()
            ->getAttribute($source ?? $target);
        $this
            ->getSymbol()
            ->setAttribute($target, $value);
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
        $this
            ->getSymbol()
            ->setAttribute($attr, $node->getId());
        return $node;
    }

    /**
     * @param string $name
     * @param string $attr
     * @return SyntaxTreeNode
     * @throws Exception
     */
    public function createRootNode(string $name, string $attr): SyntaxTreeNode
    {
        $node = $this->createNode($name, $attr);
        $this
            ->getTree()
            ->setRootNode($node);
        return $node;
    }

    /**
     * @param string $name
     * @param string $attr
     * @param string $parentAttr
     * @return SyntaxTreeNode
     * @throws Exception
     */
    public function createChildNode(string $name, string $attr, string $parentAttr): SyntaxTreeNode
    {
        $node = $this->createNode($name, $attr);
        $parentNodeId = $this
            ->getSymbol()
            ->getAttribute($parentAttr);
        $this
            ->getTree()
            ->getNode($parentNodeId)
            ->addChild($node);
        return $node;
    }

}
