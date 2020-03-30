<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\RegExp\FSM\RangeSet;

interface PropertyLoaderInterface
{
    public function getPropertyRangeSet(string $name): RangeSet;
}
