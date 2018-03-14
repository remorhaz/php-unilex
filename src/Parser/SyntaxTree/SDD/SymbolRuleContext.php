<?php

namespace Remorhaz\UniLex\Parser\SyntaxTree\SDD;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\LL1\SDD\SymbolContextInterface;
use Remorhaz\UniLex\Parser\SyntaxTree\Node;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;

class SymbolRuleContext extends TreeRuleContext implements SymbolContextInterface
{

    private $symbolIndex;

    public function __construct(Tree $tree, ParsedProduction $production, int $symbolIndex)
    {
        parent::__construct($tree, $production);
        $this->symbolIndex = $symbolIndex;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     * @throws Exception
     * @deprecated
     */
    public function setAttribute(string $name, $value)
    {
        $this
            ->getProduction()
            ->getSymbol($this->symbolIndex)
            ->setAttribute($name, $value);
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function getAttribute(string $name)
    {
        return $this
            ->getProduction()
            ->getSymbol($this->symbolIndex)
            ->getAttribute($name);
    }

    /**
     * @param string $target
     * @param string|null $source
     * @return mixed
     * @throws Exception
     */
    public function getHeaderAttribute(string $target, string $source = null)
    {
        return $this
            ->getProduction()
            ->getHeader()
            ->getAttribute($source ?? $target);
    }

    /**
     * @param string $attr
     * @return Node
     * @throws Exception
     */
    public function getNodeByAttribute(string $attr): Node
    {
        return $this
            ->getTree()
            ->getNode($this->getAttribute($attr));
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
}
