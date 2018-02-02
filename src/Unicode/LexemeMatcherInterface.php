<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\SymbolBufferInterface;

interface LexemeMatcherInterface
{

    public function match(SymbolBufferInterface $buffer, LexemeListenerInterface $lexemeListener): void;
}
