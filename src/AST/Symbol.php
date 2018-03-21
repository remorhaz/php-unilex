<?php

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class Symbol implements StackableSymbolInterface
{

    private $header;

    private $index;

    public function __construct(Node $header, int $index)
    {
        $this->header = $header;
        $this->index = $index;
    }

    public function getHeader(): Node
    {
        return $this->header;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return Node
     * @throws \Remorhaz\UniLex\Exception
     */
    public function getSymbol(): Node
    {
        return $this
            ->getHeader()
            ->getChild($this->getIndex());
    }
}
