<?php

namespace Remorhaz\UniLex\LL1Parser;

abstract class AbstractParserListener implements ParserListenerInterface
{

    public function onStart(): void
    {
    }

    public function onSymbol(ParsedSymbol $symbol): void
    {
    }

    public function onProduction(?ParsedSymbol $symbol, ParsedSymbol ...$production): void
    {
    }

    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
    }

    public function onEoi(ParsedSymbol $symbol, ParsedToken $token): void
    {
    }
}
