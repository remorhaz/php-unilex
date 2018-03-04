<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class LexemeBufferInfo implements LexemeBufferInfoInterface
{

    private $buffer;

    private $position;

    public function __construct(LexemeExtractInterface $buffer, LexemePosition $position)
    {
        $this->buffer = $buffer;
        $this->position = $position;
    }

    public function getPosition(): LexemePosition
    {
        return $this->position;
    }

    public function extract(): SplFixedArray
    {
        $position = $this->getPosition();
        return $this->buffer->extractLexeme($position);
    }
}
