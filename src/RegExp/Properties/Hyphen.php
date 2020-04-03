<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x2d), new Range(0xad), new Range(0x58a), new Range(0x1806), new Range(0x2010, 0x2011), new Range(0x2e17), new Range(0x30fb), new Range(0xfe63), new Range(0xff0d), new Range(0xff65));
