<?php

namespace Remorhaz\UniLex;

interface LexemeMatcherInterface
{

    public function match(SymbolBufferInterface $buffer, LexemeFactoryInterface $lexemeFactory): Lexeme;
}
