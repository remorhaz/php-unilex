<?php

namespace Remorhaz\UniLex;

interface SymbolFactoryInterface
{

    public function getSymbol(Token $token): int;
}
