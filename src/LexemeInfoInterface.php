<?php

namespace Remorhaz\UniLex;

use SplFixedArray;

interface LexemeInfoInterface
{

    public function getStartOffset(): int;

    public function getFinishOffset(): int;

    public function extract(): SplFixedArray;
}
