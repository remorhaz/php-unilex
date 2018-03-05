<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface TokenBufferInfoInterface
{

    public function extract(): SplFixedArray;

    public function getPosition(): TokenPosition;
}
