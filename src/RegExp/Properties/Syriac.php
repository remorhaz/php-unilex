<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\IntRangeSets\Range;
use Remorhaz\IntRangeSets\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::createUnsafe(new Range(0x700, 0x70d), new Range(0x70f, 0x74a), new Range(0x74d, 0x74f), new Range(0x860, 0x86a));
