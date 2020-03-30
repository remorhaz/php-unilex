<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x30a1, 0x30fa), new Range(0x30fd, 0x30ff), new Range(0x31f0, 0x31ff), new Range(0x32d0, 0x32fe), new Range(0x3300, 0x3357), new Range(0xff66, 0xff6f), new Range(0xff71, 0xff9d), new Range(0x1b000), new Range(0x1b164, 0x1b167));
