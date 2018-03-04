<?php

namespace Remorhaz\UniLex;

interface SymbolFactoryInterface
{

    public function getSymbol(Lexeme $lexeme): int;
}
