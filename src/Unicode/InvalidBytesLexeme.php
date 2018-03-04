<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeInfoInterface;

class InvalidBytesLexeme extends Lexeme
{

    private $info;

    public function __construct(LexemeInfoInterface $info, int $type)
    {
        parent::__construct($type, false);
        $this->info = $info;
    }

    public function getInfo(): LexemeInfoInterface
    {
        return $this->info;
    }
}
