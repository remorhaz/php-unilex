<?php

namespace Remorhaz\UniLex\LL1Parser;

interface ParserListenerInterface
{

    public function onStart(): void;

    public function onSymbol(ParsedSymbol $symbol): void;

    public function onProduction(ParsedProduction $production): void;

    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void;

    public function onEoi(ParsedSymbol $symbol, ParsedToken $token): void;
}
