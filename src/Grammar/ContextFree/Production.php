<?php

namespace Remorhaz\UniLex\Grammar\ContextFree;

class Production
{

    private $headerId;

    private $index;

    private $symbolList;

    public function __construct(int $headerId, int $index, int ...$symbolList)
    {
        $this->headerId = $headerId;
        $this->index = $index;
        $this->symbolList = $symbolList;
    }

    public function __toString()
    {
        return "{$this->getHeaderId()}:{$this->getIndex()}";
    }

    public function getHeaderId(): int
    {
        return $this->headerId;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return int[]
     */
    public function getSymbolList(): array
    {
        return $this->symbolList;
    }

    public function isEpsilon(): bool
    {
        return empty($this->getSymbolList());
    }
}
