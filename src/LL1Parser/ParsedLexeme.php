<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Lexeme;

class ParsedLexeme
{

    private $index;

    private $lexeme;

    public function __construct(int $index, Lexeme $lexeme)
    {
        $this->index = $index;
        $this->lexeme = $lexeme;
    }

    public function getLexeme(): Lexeme
    {
        return $this->lexeme;
    }

    public function getIndex(): int
    {
        return $this->index;
    }
}
