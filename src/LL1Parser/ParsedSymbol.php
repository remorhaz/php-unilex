<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParsedSymbol extends ParsedNode
{

    private $symbolId;

    public function __construct(int $index, int $symbolId)
    {
        parent::__construct($index);
        $this->symbolId = $symbolId;
    }

    public function getSymbolId(): int
    {
        return $this->symbolId;
    }
}
