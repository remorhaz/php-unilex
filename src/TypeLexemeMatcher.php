<?php

namespace Remorhaz\UniLex;

class TypeLexemeMatcher implements LexemeMatcherInterface
{

    public function match(SymbolBufferInterface $buffer): Lexeme
    {
        $type = $buffer->getSymbol();
        $lexeme = new Lexeme($type);
        $buffer->nextSymbol();
        return $lexeme;
    }
}
