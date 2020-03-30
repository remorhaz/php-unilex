<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x16b00, 0x16b45), new Range(0x16b50, 0x16b59), new Range(0x16b5b, 0x16b61), new Range(0x16b63, 0x16b77), new Range(0x16b7d, 0x16b8f));
