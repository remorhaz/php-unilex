<?php

namespace Remorhaz\UniLex;

class EoiLexeme extends Lexeme
{
    private $info;

    public function __construct(LexemeInfoInterface $info, int $type)
    {
        parent::__construct($type);
        $this->info = $info;
    }

    public function getInfo(): LexemeInfoInterface
    {
        return $this->info;
    }
}
