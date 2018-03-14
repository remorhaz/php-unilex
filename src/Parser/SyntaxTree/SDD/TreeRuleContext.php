<?php

namespace Remorhaz\UniLex\Parser\SyntaxTree\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\SyntaxTree\Node;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;

abstract class TreeRuleContext
{

    private $tree;

    private $production;

    public function __construct(Tree $tree, ParsedProduction $production)
    {
        $this->tree = $tree;
        $this->production = $production;
    }

    private function getTree(): Tree
    {
        return $this->tree;
    }

    private function getProduction(): ParsedProduction
    {
        return $this->production;
    }

    public function createNode(string $name): Node
    {
        return $this
            ->getTree()
            ->createNode($name);
    }

    /**
     * @param Node $node
     * @return $this
     * @throws Exception
     */
    public function setRootNode(Node $node)
    {
        $this
            ->getTree()
            ->setRootNode($node);
        return $this;
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
     * @param int $index
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function getNodeBySymbolAttribute(int $index, string $attr): Node
    {
        return $this
            ->getTree()
            ->getNode($this->getSymbolAttribute($index, $attr));
    }

    /**
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function getNodeByHeaderAttribute(string $attr): Node
    {
        return $this
            ->getTree()
            ->getNode($this->getHeaderAttribute($attr));
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
}
