<?php

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\Exception;

class Production
{

    private $header;

    private $index;

    private $symbolList;

    public function __construct(Symbol $header, int $index, Symbol ...$symbolList)
    {
        $this->header = $header;
        $this->index = $index;
        $this->symbolList = $symbolList;
    }

    public function __toString()
    {
        return "{$this->header->getSymbolId()}:{$this->index}";
    }

    public function getHeader(): Symbol
    {
        return $this->header;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return Symbol[]
     */
    public function getSymbolList(): array
    {
        return $this->symbolList;
    }

    /**
     * @param int $index
     * @return Symbol
     * @throws Exception
     */
    public function getSymbol(int $index): Symbol
    {
        if (!isset($this->symbolList[$index])) {
            throw new Exception("Symbol at index {$index} is undefined in production {$this}");
        }
        return $this->symbolList[$index];
    }

    public function isEpsilon(): bool
    {
        return empty($this->getSymbolList());
    }
}
