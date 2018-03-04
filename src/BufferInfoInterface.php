<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface BufferInfoInterface
{

    public function extract(): SplFixedArray;

    public function getPosition(): LexemePosition;
}
