<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeInfoInterface;

class SymbolLexeme extends Lexeme
{

    private $symbol;

    private $info;

    public function __construct(LexemeInfoInterface $info, int $symbol)
    {
        parent::__construct($symbol);
        $this->info = $info;
        $this->symbol = $symbol;
    }

    public function getSymbol(): int
    {
        return $this->symbol;
    }

    public function getInfo(): LexemeInfoInterface
    {
        return $this->info;
    }
}
