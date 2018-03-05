<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Lexeme;

class ParseTreeLexemeNode implements ParseTreeNodeInterface
{

    private $lexeme;

    public function __construct(Lexeme $lexeme)
    {
        $this->lexeme = $lexeme;
    }

    public function getLexeme(): Lexeme
    {
        return $this->lexeme;
    }
}