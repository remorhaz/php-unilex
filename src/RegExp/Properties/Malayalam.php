<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0xd00, 0xd0c), new Range(0xd0e, 0xd10), new Range(0xd12, 0xd44), new Range(0xd46, 0xd48), new Range(0xd4a, 0xd4f), new Range(0xd54, 0xd63), new Range(0xd66, 0xd7f));
