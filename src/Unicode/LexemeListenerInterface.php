<?php

namespace Remorhaz\UniLex\Unicode;

interface LexemeListenerInterface
{

    public function onSymbol(SymbolLexeme $lexeme);

    public function onInvalidBytes(InvalidBytesLexeme $lexeme);
}
