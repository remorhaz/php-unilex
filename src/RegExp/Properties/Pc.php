<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\IntRangeSets\Range;
use Remorhaz\IntRangeSets\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::createUnsafe(new Range(0x5f), new Range(0x203f, 0x2040), new Range(0x2054), new Range(0xfe33, 0xfe34), new Range(0xfe4d, 0xfe4f), new Range(0xff3f));
