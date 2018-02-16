<?php

namespace Remorhaz\UniLex\RegExp;

/**
 * List of all available UniLex regular expression tokens.
 *
 * @see doc/RegExpGrammar.md
 */
abstract class TokenType
{
    const CTL_ASCII             = 0x01;
    const DOLLAR                = 0x02;
    const LEFT_BRACKET          = 0x03;
    const RIGHT_BRACKET         = 0x04;
    const STAR                  = 0x05;
    const PLUS                  = 0x06;
    const COMMA                 = 0x07;
    const HYPHEN                = 0x08;
    const DOT                   = 0x09;
    const DIGIT_ZERO            = 0x0A;
    const DIGIT_OCT             = 0x0B;
    const DIGIT_DEC             = 0x0C;
    const QUESTION              = 0x0D;
    const CAPITAL_P             = 0x0E;
    const LEFT_SQUARE_BRACKET   = 0x0F;
    const BACKSLASH             = 0x10;
    const RIGHT_SQUARE_BRACKET  = 0x11;
    const CIRCUMFLEX            = 0x12;
    const SMALL_C               = 0x13;
    const SMALL_O               = 0x14;
    const SMALL_P               = 0x15;
    const SMALL_U               = 0x16;
    const SMALL_X               = 0x17;
    const LEFT_CURLY_BRACKET    = 0x18;
    const VERTICAL_LINE         = 0x19;
    const RIGHT_CURLY_BRACKET   = 0x1A;
    const OTHER_HEX_LETTER      = 0x1B;
    const OTHER_ASCII_LETTER    = 0x1C;
    const PRINTABLE_ASCII_OTHER = 0x1D;
    const OTHER_ASCII           = 0x1F;
    const NOT_ASCII             = 0x20;
    const INVALID               = 0xFF;
}
