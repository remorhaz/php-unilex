<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Remorhaz\UniLex\Tool\RegExp\Properties;

use Remorhaz\UniLex\RegExp\FSM\Range;
use Remorhaz\UniLex\RegExp\FSM\RangeSet;

/** phpcs:disable Generic.Files.LineLength.TooLong */
return RangeSet::loadUnsafe(new Range(0x34f), new Range(0x115f, 0x1160), new Range(0x17b4, 0x17b5), new Range(0x2065), new Range(0x3164), new Range(0xffa0), new Range(0xfff0, 0xfff8), new Range(0xe0000), new Range(0xe0002, 0xe001f), new Range(0xe0080, 0xe00ff), new Range(0xe01f0, 0xe0fff));
