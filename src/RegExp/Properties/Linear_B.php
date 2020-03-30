<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x10000, 0x1000b), new Range(0x1000d, 0x10026), new Range(0x10028, 0x1003a), new Range(0x1003c, 0x1003d), new Range(0x1003f, 0x1004d), new Range(0x10050, 0x1005d), new Range(0x10080, 0x100fa));
