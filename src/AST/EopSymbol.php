<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class EopSymbol implements StackableSymbolInterface
{
    public function __construct(
        private Node $node,
    ) {
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
