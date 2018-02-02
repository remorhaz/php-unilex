<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeInfoInterface;

class SymbolLexeme extends Lexeme
{

    private $symbol;

    public function __construct(LexemeInfoInterface $info, int $symbol)
    {
        parent::__construct($info);
        $this->symbol = $symbol;
    }

    public function getSymbol(): int
    {
        return $this->symbol;
    }
}
