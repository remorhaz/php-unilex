<?php

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\Stack\StackableSymbolInterface;

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
