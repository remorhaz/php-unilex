<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexeme;

class ScannerLexemeListener implements LexemeListenerInterface
{

    private $lexeme;

    public function onSymbol(SymbolLexeme $lexeme)
    {
        $this->lexeme = $lexeme;
    }

    public function onInvalidBytes(InvalidBytesLexeme $lexeme)
    {
        $this->lexeme = $lexeme;
    }

    public function resetLexeme(): void
    {
        unset($this->lexeme);
    }

    public function getLexeme(): Lexeme
    {
        return $this->lexeme;
    }
}
