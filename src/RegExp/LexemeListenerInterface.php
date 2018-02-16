<?php

namespace Remorhaz\UniLex\RegExp;

interface LexemeListenerInterface
{

    public function onToken(Lexeme $lexeme): void;

    public function onInvalidToken(Lexeme $lexeme): void;
}
