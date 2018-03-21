<?php

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class EopSymbol implements StackableSymbolInterface
{

    private $production;

    public function __construct(Production $production)
    {
        $this->production = $production;
    }

    public function getProduction(): Production
    {
        return $this->production;
    }
}
