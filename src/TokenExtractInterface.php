<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface TokenExtractInterface
{

    public function extractToken(TokenPosition $position): SplFixedArray;
}
