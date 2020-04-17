<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\IntRangeSets\Range;
use Remorhaz\IntRangeSets\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::createUnsafe(new Range(0x10800, 0x10805), new Range(0x10808), new Range(0x1080a, 0x10835), new Range(0x10837, 0x10838), new Range(0x1083c), new Range(0x1083f));
