<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class LexemeInfo extends SymbolBufferLexemeInfo
{

    private $sourcePosition;

    public function __construct(SymbolBuffer $buffer, LexemePosition $position, LexemePosition $sourcePosition)
    {
        parent::__construct($buffer, $position);
        $this->sourcePosition = $sourcePosition;
    }

    public function getSourcePosition(): LexemePosition
    {
        return $this->sourcePosition;
    }
}
