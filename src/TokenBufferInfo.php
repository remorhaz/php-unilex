<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

class TokenBufferInfo implements TokenBufferInfoInterface
{

    private $buffer;

    private $position;

    public function __construct(TokenExtractInterface $buffer, TokenPosition $position)
    {
        $this->buffer = $buffer;
        $this->position = $position;
    }

    public function getPosition(): TokenPosition
    {
        return $this->position;
    }

    public function extract(): SplFixedArray
    {
        $position = $this->getPosition();
        return $this->buffer->extractToken($position);
    }
}
