<?php

namespace Remorhaz\UniLex\SyntaxTree\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\SDD\SymbolContextInterface;
use Remorhaz\UniLex\SyntaxTree\Tree;
use Remorhaz\UniLex\SyntaxTree\Node;

class SymbolRuleContext implements SymbolContextInterface
{

    private $tree;

    private $production;

    private $symbolIndex;

    public function __construct(Tree $tree, ParsedProduction $production, int $symbolIndex)
    {
        $this->tree = $tree;
        $this->production = $production;
        $this->symbolIndex = $symbolIndex;
    }

    private function getTree(): Tree
    {
        return $this->tree;
    }

    private function getProduction(): ParsedProduction
    {
        return $this->production;
    }

    private function getSymbolIndex(): int
    {
        return $this->symbolIndex;
    }

    /**
     * @return ParsedSymbol
     * @throws Exception
     */
    private function getSymbol(): ParsedSymbol
    {
        return $this
            ->getProduction()
            ->getSymbol($this->getSymbolIndex());
    }

    /**
     * @param string $name
     * @param $value
     * @return SymbolRuleContext
     * @throws Exception
     */
    public function setAttribute(string $name, $value): self
    {
        $this
            ->getSymbol()
            ->setAttribute($name, $value);
        return $this;
    }

    /**
     * @param string $target
     * @param string|null $source
     * @return SymbolRuleContext
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
     * @param int $symbolIndex
     * @param string $target
     * @param string|null $source
     * @return SymbolRuleContext
     * @throws Exception
     */
    public function inheritSymbolAttribute(int $symbolIndex, string $target, string $source = null): self
    {
        if ($symbolIndex >= $this->getSymbolIndex()) {
            $indexText = "from symbol {$symbolIndex} to {$this->getSymbolIndex()}";
            throw new Exception("L-attribute SDD forbids attribute inheritance {$indexText}");
        }
        $value = $this
            ->getProduction()
            ->getSymbol($symbolIndex)
            ->getAttribute($source ?? $target);
        $this
            ->getSymbol()
            ->setAttribute($target, $value);
        return $this;
    }

    /**
     * @param string $name
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function createNode(string $name, string $attr): Node
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
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function getNode(string $attr): Node
    {
        $nodeId = $this
            ->getSymbol()
            ->getAttribute($attr);
        return $this
            ->getTree()
            ->getNode($nodeId);
    }
}
