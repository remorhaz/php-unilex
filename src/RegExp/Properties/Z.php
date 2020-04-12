<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x20), new Range(0xa0), new Range(0x1680), new Range(0x2000, 0x200a), new Range(0x2028, 0x2029), new Range(0x202f), new Range(0x205f), new Range(0x3000));