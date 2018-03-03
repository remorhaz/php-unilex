<?php

namespace Remorhaz\UniLex;

class Lexeme
{

    private $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
