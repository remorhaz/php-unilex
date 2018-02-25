<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface LexemeInfoInterface
{

    public function extract(): SplFixedArray;

    public function getPosition(): LexemePosition;
}
