<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x30, 0x39), new Range(0x41, 0x46), new Range(0x61, 0x66), new Range(0xff10, 0xff19), new Range(0xff21, 0xff26), new Range(0xff41, 0xff46));
