<?php

namespace Remorhaz\UniLex;

class LexemePosition
{

    private $startOffset;

    private $finishOffset;

    public function __construct(int $startOffset, int $finishOffset)
    {
        $this->startOffset = $startOffset;
        $this->finishOffset = $finishOffset;
    }

    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    public function getFinishOffset(): int
    {
        return $this->finishOffset;
    }
}
