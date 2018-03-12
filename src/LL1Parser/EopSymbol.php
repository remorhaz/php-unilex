<?php

namespace Remorhaz\UniLex\LL1Parser;

class EopSymbol implements StackableSymbolInterface
{

    private $production;

    public function __construct(ParsedProduction $production)
    {
        $this->production = $production;
    }

    public function getProduction(): ParsedProduction
    {
        return $this->production;
    }
}
