<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\IntRangeSets\Range;
use Remorhaz\IntRangeSets\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::createUnsafe(new Range(0x22), new Range(0x27), new Range(0xab), new Range(0xbb), new Range(0x2018, 0x201f), new Range(0x2039, 0x203a), new Range(0x2e42), new Range(0x300c, 0x300f), new Range(0x301d, 0x301f), new Range(0xfe41, 0xfe44), new Range(0xff02), new Range(0xff07), new Range(0xff62, 0xff63));
