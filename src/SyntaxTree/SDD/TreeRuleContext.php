<?php

namespace Remorhaz\UniLex\SyntaxTree\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\SyntaxTree\Node;
use Remorhaz\UniLex\SyntaxTree\Tree;

abstract class TreeRuleContext
{

    private $tree;

    private $production;

    public function __construct(Tree $tree, ParsedProduction $production)
    {
        $this->tree = $tree;
        $this->production = $production;
    }

    abstract public function setAttribute(string $name, $value);

    abstract public function getAttribute(string $name);

    protected function getTree(): Tree
    {
        return $this->tree;
    }

    protected function getProduction(): ParsedProduction
    {
        return $this->production;
    }

    /**
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function getNode(string $attr): Node
    {
        return $this
            ->getTree()
            ->getNode($this->getAttribute($attr));
    }

    /**
     * @param int $index
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function getSymbolNode(int $index, string $attr): Node
    {
        return $this
            ->getTree()
            ->getNode($this->getSymbolAttribute($index, $attr));
    }

    /**
     * @param string $name
     * @param string $attr
     * @return Node
     */
    public function createNode(string $name, string $attr): Node
    {
        $node = $this
            ->getTree()
            ->createNode($name);
        $this->setAttribute($attr, $node->getId());
        return $node;
    }

    /**
     * @param string $attr
     * @return $this
     * @throws Exception
     */
    public function setRootNode(string $attr)
    {
        $this
            ->getTree()
            ->setRootNode($this->getNode($attr));
        return $this;
    }

    /**
     * @param string $target
     * @param string $source
     * @return $this
     */
    public function copyAttribute(string $target, string $source)
    {
        $this->setAttribute($target, $this->getAttribute($source));
        return $this;
    }

    /**
     * @param int $symbolIndex
     * @param string $target
     * @param string|null $source
     * @return $this
     * @throws Exception
     */
    public function copySymbolAttribute(int $symbolIndex, string $target, string $source = null)
    {
        $value = $this->getSymbolAttribute($symbolIndex, $source ?? $target);
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
}
