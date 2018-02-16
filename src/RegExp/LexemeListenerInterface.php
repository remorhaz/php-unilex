<?php

namespace Remorhaz\UniLex\RegExp;

interface LexemeListenerInterface
{

    public function onValidSymbol(Lexeme $lexeme): void;

    public function onInvalidSymbol(Lexeme $lexeme): void;
}
