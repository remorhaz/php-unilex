<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParsedProduction
{

    private $symbol;

    private $index;

    private $symbolList;

    public function __construct(ParsedSymbol $symbol, int $index, ParsedSymbol ...$symbolList)
    {
        $this->symbol = $symbol;
        $this->index = $index;
        $this->symbolList = $symbolList;
    }

    public function getSymbol(): ParsedSymbol
    {
        return $this->symbol;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return ParsedSymbol[]
     */
    public function getSymbolList(): array
    {
        return $this->symbolList;
    }
}
