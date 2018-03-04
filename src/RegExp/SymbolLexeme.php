<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeInfoInterface;

class SymbolLexeme extends Lexeme
{

    private $symbol;
    private $info;

    public function __construct(LexemeInfoInterface $info, int $type, int $symbol)
    {
        parent::__construct($type, false);
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
