<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0xab), new Range(0x2018), new Range(0x201b, 0x201c), new Range(0x201f), new Range(0x2039), new Range(0x2e02), new Range(0x2e04), new Range(0x2e09), new Range(0x2e0c), new Range(0x2e1c), new Range(0x2e20));
