<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\EoiLexeme;
use Remorhaz\UniLex\Lexeme;

abstract class AbstractParserListener implements ParserListenerInterface
{

    public function onSymbol(int $symbolId, Lexeme $lexeme): void
    {
    }

    public function onLexeme(Lexeme $lexeme): void
    {
    }

    public function onEoi(EoiLexeme $lexeme): void
    {
    }
}
