<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface LexemeBufferInfoInterface
{

    public function extract(): SplFixedArray;

    public function getPosition(): LexemePosition;
}
