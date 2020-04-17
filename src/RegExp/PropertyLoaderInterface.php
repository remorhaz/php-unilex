<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\IntRangeSets\RangeSetInterface;

interface PropertyLoaderInterface
{
    public function getRangeSet(string $propertyName): RangeSetInterface;
}
