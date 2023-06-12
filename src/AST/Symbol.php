<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class Symbol implements StackableSymbolInterface
{
    private ?Node $symbol = null;

    public function __construct(
        private Node $header,
        private int $index,
    ) {
    }

    public function setSymbol(Node $node): void
    {
        $this->symbol = $node;
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
     * @throws Exception
     */
    public function getSymbol(): Node
    {
        return $this->symbol ??= $this
            ->getHeader()
            ->getChild($this->getIndex());
    }
}
