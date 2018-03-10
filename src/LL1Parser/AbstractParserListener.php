<?php

namespace Remorhaz\UniLex\LL1Parser;

abstract class AbstractParserListener implements ParserListenerInterface
{

    public function onStart(): void
    {
    }

    public function onRootSymbol(ParsedSymbol $symbol): void
    {
    }

    public function onProduction(ParsedProduction $production): void
    {
    }

    public function onSymbol(int $symbolIndex, ParsedProduction $production): void
    {
    }

    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
    }

    public function onEoi(ParsedSymbol $symbol, ParsedToken $token): void
    {
    }
}
