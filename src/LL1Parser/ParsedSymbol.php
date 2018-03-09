<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParsedSymbol
{

    private $index;

    private $id;

    public function __construct(int $index, int $symbolId)
    {
        $this->index = $index;
        $this->id = $symbolId;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getSymbolId(): int
    {
        return $this->id;
    }
}
