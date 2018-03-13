<?php

namespace Remorhaz\UniLex\Parser\LL1\SDD;

use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;

interface ContextFactoryInterface
{

    public function createSymbolContext(ParsedProduction $production, int $symbolIndex): SymbolContextInterface;

    public function createProductionContext(ParsedProduction $production): ProductionContextInterface;

    public function createTokenContext(ParsedSymbol $symbol, ParsedToken $token): TokenContextInterface;
}
