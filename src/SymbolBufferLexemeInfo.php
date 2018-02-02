<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class SymbolBufferLexemeInfo implements LexemeInfoInterface
{

    private $buffer;

    private $startOffset;

    private $finishOffset;

    public function __construct(SymbolBuffer $buffer, int $startOffset, int $finishOffset)
    {
        $this->buffer = $buffer;
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

    public function extract(): SplFixedArray
    {
        return $this->buffer->extractLexeme($this->startOffset, $this->finishOffset);
    }
}
