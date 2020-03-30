<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x1a20, 0x1a5e), new Range(0x1a60, 0x1a7c), new Range(0x1a7f, 0x1a89), new Range(0x1a90, 0x1a99), new Range(0x1aa0, 0x1aad));
