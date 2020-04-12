<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x2160, 0x216f), new Range(0x24b6, 0x24cf), new Range(0x1f130, 0x1f149), new Range(0x1f150, 0x1f169), new Range(0x1f170, 0x1f189));