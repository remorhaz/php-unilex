<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x1100, 0x11ff), new Range(0x302e, 0x302f), new Range(0x3131, 0x318e), new Range(0x3200, 0x321e), new Range(0x3260, 0x327e), new Range(0xa960, 0xa97c), new Range(0xac00, 0xd7a3), new Range(0xd7b0, 0xd7c6), new Range(0xd7cb, 0xd7fb), new Range(0xffa0, 0xffbe), new Range(0xffc2, 0xffc7), new Range(0xffca, 0xffcf), new Range(0xffd2, 0xffd7), new Range(0xffda, 0xffdc));
