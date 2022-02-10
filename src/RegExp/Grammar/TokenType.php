<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\Grammar;

/**
 * List of all available UniLex regular expression tokens.
 *
 * @see doc/RegExpGrammar.md
 */
abstract class TokenType
{
    public const CTL_ASCII              = 0x01;
    public const DOLLAR                 = 0x02;
    public const LEFT_BRACKET           = 0x03;
    public const RIGHT_BRACKET          = 0x04;
    public const STAR                   = 0x05;
    public const PLUS                   = 0x06;
    public const COMMA                  = 0x07;
    public const HYPHEN                 = 0x08;
    public const DOT                    = 0x09;
    public const DIGIT_ZERO             = 0x0A;
    public const DIGIT_OCT              = 0x0B;
    public const DIGIT_DEC              = 0x0C;
    public const QUESTION               = 0x0D;
    public const CAPITAL_P              = 0x0E;
    public const LEFT_SQUARE_BRACKET    = 0x0F;
    public const BACKSLASH              = 0x10;
    public const RIGHT_SQUARE_BRACKET   = 0x11;
    public const CIRCUMFLEX             = 0x12;
    public const SMALL_C                = 0x13;
    public const SMALL_O                = 0x14;
    public const SMALL_P                = 0x15;
    public const SMALL_U                = 0x16;
    public const SMALL_X                = 0x17;
    public const LEFT_CURLY_BRACKET     = 0x18;
    public const VERTICAL_LINE          = 0x19;
    public const RIGHT_CURLY_BRACKET    = 0x1A;
    public const OTHER_HEX_LETTER       = 0x1B;
    public const OTHER_ASCII_LETTER     = 0x1C;
    public const PRINTABLE_ASCII_OTHER  = 0x1D;
    public const OTHER_ASCII            = 0x1F;
    public const NOT_ASCII              = 0x20;
    public const INVALID                = 0xFE;
    public const EOI                    = 0xFF;
}
