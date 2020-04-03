<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0xbb), new Range(0x2019), new Range(0x201d), new Range(0x203a), new Range(0x2e03), new Range(0x2e05), new Range(0x2e0a), new Range(0x2e0d), new Range(0x2e1d), new Range(0x2e21));
