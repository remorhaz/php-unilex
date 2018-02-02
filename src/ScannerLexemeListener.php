<?php

namespace Remorhaz\UniLex;

use Remorhaz\UniLex\Unicode\InvalidBytesLexeme;
use Remorhaz\UniLex\Unicode\LexemeListenerInterface;
use Remorhaz\UniLex\Unicode\SymbolLexeme;

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
