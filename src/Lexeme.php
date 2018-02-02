<?php

namespace Remorhaz\UniLex;

abstract class Lexeme
{

    protected $info;

    public function __construct(LexemeInfoInterface $info)
    {
        $this->info = $info;
    }

    public function getInfo(): LexemeInfoInterface
    {
        return $this->info;
    }
}