<?php

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class EopSymbol implements StackableSymbolInterface
{

    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
