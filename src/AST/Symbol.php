<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\AST;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class Symbol implements StackableSymbolInterface
{
    private $header;

    private $index;

    private $symbol;

    public function __construct(Node $header, int $index)
    {
        $this->header = $header;
        $this->index = $index;
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
     * @return Node
     * @throws Exception
     */
    public function getSymbol(): Node
    {
        if (!isset($this->symbol)) {
            $this->symbol = $this
                ->getHeader()
                ->getChild($this->getIndex());
        }
        return $this->symbol;
    }
}
