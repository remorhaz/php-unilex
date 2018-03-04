<?php

namespace Remorhaz\UniLex;

class LexemeMatcherByType implements LexemeMatcherInterface
{

    public function match(SymbolBufferInterface $buffer, LexemeFactoryInterface $lexemeFactory): Lexeme
    {
        $tokenId = $buffer->getSymbol();
        $lexeme = $lexemeFactory->createLexeme($tokenId);
        $buffer->nextSymbol();
        return $lexeme;
    }
}
