<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface LexemeExtractInterface
{

    public function extractLexeme(LexemePosition $position): SplFixedArray;
}
