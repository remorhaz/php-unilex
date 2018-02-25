<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\LexemeExtractInterface;
use Remorhaz\UniLex\LexemePosition;
use Remorhaz\UniLex\SymbolBufferLexemeInfo;

class LexemeInfo extends SymbolBufferLexemeInfo
{

    private $sourcePosition;

    public function __construct(LexemeExtractInterface $buffer, LexemePosition $position, LexemePosition $sourcePosition)
    {
        parent::__construct($buffer, $position);
        $this->sourcePosition = $sourcePosition;
    }

    public function getSourcePosition(): LexemePosition
    {
        return $this->sourcePosition;
    }
}
