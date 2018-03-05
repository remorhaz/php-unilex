<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParsedSymbol
{

    private $index;

    private $id;

    private $productionIndex;

    public function __construct(int $index, int $symbolId, int $productionIndex)
    {
        $this->index = $index;
        $this->id = $symbolId;
        $this->productionIndex;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getSymbolId(): int
    {
        return $this->id;
    }

    public function getProductionIndex(): int
    {
        return $this->productionIndex;
    }
}
