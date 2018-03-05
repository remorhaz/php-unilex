<?php

namespace Remorhaz\UniLex\LL1Parser;

class ParsedSymbol
{

    private $index;

    private $id;

    public function __construct(int $index, int $id)
    {
        $this->index = $index;
        $this->id = $id;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
