<?php

namespace Remorhaz\UniLex\LL1Parser\SDD;

use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;

interface ContextFactoryInterface
{

    public function createSymbolContext(ParsedProduction $production, int $symbolIndex): SymbolContextInterface;

    public function createTokenContext(ParsedSymbol $symbol, ParsedToken $token): TokenContextInterface;
}
