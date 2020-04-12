<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x21, 0x2f), new Range(0x3a, 0x40), new Range(0x5b, 0x5e), new Range(0x60), new Range(0x7b, 0x7e), new Range(0xa1, 0xa7), new Range(0xa9), new Range(0xab, 0xac), new Range(0xae), new Range(0xb0, 0xb1), new Range(0xb6), new Range(0xbb), new Range(0xbf), new Range(0xd7), new Range(0xf7), new Range(0x2010, 0x2027), new Range(0x2030, 0x203e), new Range(0x2041, 0x2053), new Range(0x2055, 0x205e), new Range(0x2190, 0x245f), new Range(0x2500, 0x2775), new Range(0x2794, 0x2bff), new Range(0x2e00, 0x2e7f), new Range(0x3001, 0x3003), new Range(0x3008, 0x3020), new Range(0x3030), new Range(0xfd3e, 0xfd3f), new Range(0xfe45, 0xfe46));