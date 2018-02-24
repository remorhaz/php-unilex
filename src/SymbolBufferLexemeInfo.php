<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class SymbolBufferLexemeInfo implements LexemeInfoInterface
{

    private $buffer;

    private $startOffset;

    private $finishOffset;

    private $parentInfo;

    public function __construct(
        SymbolBuffer $buffer,
        int $startOffset,
        int $finishOffset,
        LexemeInfoInterface $parentInfo = null
    ) {
        $this->buffer = $buffer;
        $this->startOffset = $startOffset;
        $this->finishOffset = $finishOffset;
        $this->parentInfo = $parentInfo;
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

    public function getParentInfo(): ?LexemeInfoInterface
    {
        return $this->parentInfo;
    }
}
