<?php

namespace Remorhaz\UniLex\IO;

use Remorhaz\UniLex\Lexer\TokenPosition;
use SplFixedArray;

interface TokenExtractInterface
{

    public function extractToken(TokenPosition $position): SplFixedArray;
}
