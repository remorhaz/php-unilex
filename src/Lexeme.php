<?php

namespace Remorhaz\UniLex;

class Lexeme
{

    private $type;

    private $isEoi;

    public function __construct(int $type, bool $isEoi)
    {
        $this->type = $type;
        $this->isEoi = $isEoi;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function isEoi(): bool
    {
        return $this->isEoi;
    }
}
