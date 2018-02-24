<?php

namespace Remorhaz\UniLex;

interface LexemeMatcherInterface
{

    public function match(SymbolBufferInterface $buffer): Lexeme;
}
