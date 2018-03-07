<?php

namespace Remorhaz\UniLex\Grammar\ContextFree;

class Production
{

    private $symbolId;

    private $index;

    private $symbolList;

    public function __construct(int $symbolId, int $index, int ...$symbolList)
    {
        $this->symbolId = $symbolId;
        $this->index = $index;
        $this->symbolList = $symbolList;
    }

    public function __toString()
    {
        return "{$this->getSymbolId()}:{$this->getIndex()}";
    }

    public function getSymbolId(): int
    {
        return $this->symbolId;
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
