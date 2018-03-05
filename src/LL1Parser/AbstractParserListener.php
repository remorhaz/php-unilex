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

    public function onLexeme(ParsedSymbol $symbol, ParsedLexeme $lexeme): void
    {
    }

    public function onEoi(ParsedSymbol $symbol, ParsedLexeme $lexeme): void
    {
    }
}
