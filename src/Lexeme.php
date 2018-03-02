<?php

namespace Remorhaz\UniLex;

abstract class Lexeme
{

    private $info;

    private $type;

    public function __construct(LexemeInfoInterface $info, int $type)
    {
        $this->info = $info;
        $this->type = $type;
    }

    public function getInfo(): LexemeInfoInterface
    {
        return $this->info;
    }

    public function getType(): int
    {
        return $this->type;
    }
}