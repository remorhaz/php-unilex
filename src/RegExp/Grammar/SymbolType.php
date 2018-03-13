<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

/**
 * List of all available UniLex regular expression productions.
 *
 * @see doc/RegExpGrammar.md
 */
abstract class SymbolType
{
    const NT_ROOT                       = 0x00;

    const NT_PARTS                      = 0x01;
    const NT_ALT_PARTS                  = 0x02;
    const NT_ALT_SEPARATOR              = 0x03;
    const NT_PART                       = 0x04;
    const NT_ITEM                       = 0x05;
    const NT_ASSERT                     = 0x06;
    const NT_ASSERT_LINE_START          = 0x07;
    const NT_ASSERT_LINE_FINISH         = 0x08;
    const NT_ITEM_BODY                  = 0x09;
    const NT_GROUP                      = 0x0A;
    const NT_GROUP_START                = 0x0B;
    const NT_GROUP_END                  = 0x0C;
    const NT_CLASS_                     = 0x0D;
    const NT_CLASS_START                = 0x0E;
    const NT_CLASS_INVERTOR             = 0x0F;
    const NT_CLASS_BODY                 = 0x10;
    const NT_FIRST_CLASS_ITEM           = 0x11;
    const NT_CLASS_ITEM                 = 0x12;
    const NT_FIRST_UNESC_CLASS_SYMBOL   = 0x13;
    const NT_CLASS_SYMBOL               = 0x14;
    const NT_ESC_CLASS_SYMBOL           = 0x15;
    const NT_ESC                        = 0x16;
    const NT_CLASS_ESC_SEQUENCE         = 0x17;
    const NT_UNESC_CLASS_SYMBOL         = 0x18;
    const NT_RANGE                      = 0x19;
    const NT_RANGE_SEPARATOR            = 0x1A;
    const NT_SYMBOL                     = 0x1C;
    const NT_SYMBOL_ANY                 = 0x1D;
    const NT_ESC_SYMBOL                 = 0x1E;
    const NT_ESC_SEQUENCE               = 0x1F;
    const NT_ESC_SIMPLE                 = 0x20;
    const NT_ESC_SPECIAL                = 0x21;
    const NT_ESC_NON_PRINTABLE          = 0x22;
    const NT_ESC_CTL                    = 0x23;
    const NT_ESC_CTL_MARKER             = 0x24;
    const NT_ESC_CTL_CODE               = 0x25;
    const NT_ESC_NUM_START              = 0x26;
    const NT_ESC_NUM_FINISH             = 0x27;
    const NT_ESC_OCT                    = 0x28;
    const NT_ESC_OCT_SHORT              = 0x29;
    const NT_ESC_OCT_SHORT_NUM          = 0x2A;
    const NT_ESC_OCT_SHORT_MARKER       = 0x2B;
    const NT_ESC_OCT_LONG               = 0x2C;
    const NT_ESC_OCT_LONG_NUM           = 0x2D;
    const NT_ESC_OCT_LONG_MARKER        = 0x2E;
    const NT_ESC_HEX                    = 0x2F;
    const NT_ESC_HEX_MARKER             = 0x30;
    const NT_ESC_HEX_NUM                = 0x31;
    const NT_ESC_HEX_SHORT_NUM          = 0x32;
    const NT_ESC_HEX_LONG_NUM           = 0x33;
    const NT_ESC_UNICODE                = 0x34;
    const NT_ESC_UNICODE_MARKER         = 0x35;
    const NT_ESC_UNICODE_NUM            = 0x36;
    const NT_ESC_PROP                   = 0x37;
    const NT_ESC_NOT_PROP               = 0x38;
    const NT_ESC_PROP_MARKER            = 0x39;
    const NT_ESC_NOT_PROP_MARKER        = 0x3A;
    const NT_PROP                       = 0x3B;
    const NT_PROP_SHORT                 = 0x3C;
    const NT_PROP_FULL                  = 0x3D;
    const NT_PROP_START                 = 0x3E;
    const NT_PROP_FINISH                = 0x3F;
    const NT_PROP_NAME                  = 0x40;
    const NT_NOT_PROP_START             = 0x41;
    const NT_NOT_PROP_FINISH            = 0x42;
    const NT_UNESC_SYMBOL               = 0x43;
    const NT_ITEM_QUANT                 = 0x44;
    const NT_ITEM_OPT                   = 0x45;
    const NT_ITEM_QUANT_STAR            = 0x46;
    const NT_ITEM_QUANT_PLUS            = 0x47;
    const NT_LIMIT                      = 0x48;
    const NT_LIMIT_START                = 0x49;
    const NT_LIMIT_END                  = 0x4A;
    const NT_LIMIT_SEPARATOR            = 0x4B;
    const NT_MIN                        = 0x4C;
    const NT_MAX                        = 0x4D;
    const NT_OCT_DIGIT                  = 0x4E;
    const NT_OCT                        = 0x4F;
    const NT_DEC_DIGIT                  = 0x50;
    const NT_DEC                        = 0x51;
    const NT_HEX_DIGIT                  = 0x52;
    const NT_HEX                        = 0x53;
    const NT_META_CHAR                  = 0x54;
    const NT_ASCII_LETTER               = 0x55;
    const NT_PRINTABLE_ASCII            = 0x56;
    const NT_CLASS_END                  = 0x57;
    const NT_PRINTABLE_ASCII_OTHER      = 0x58;
    const NT_CLASS_ITEMS                = 0x59;
    const NT_ESC_OCT_SHORT_NUM_LAST     = 0x5A;
    const NT_PROP_NAME_PART             = 0x5B;
    const NT_OPT_MAX                    = 0x5C;
    const NT_OPT_OCT                    = 0x5D;
    const NT_OPT_DEC                    = 0x5E;
    const NT_OPT_HEX                    = 0x5F;

    const T_CTL_ASCII                   = 0x60;
    const T_DOLLAR                      = 0x61;
    const T_LEFT_BRACKET                = 0x62;
    const T_RIGHT_BRACKET               = 0x63;
    const T_STAR                        = 0x64;
    const T_PLUS                        = 0x65;
    const T_COMMA                       = 0x66;
    const T_HYPHEN                      = 0x67;
    const T_DOT                         = 0x68;
    const T_DIGIT_ZERO                  = 0x69;
    const T_DIGIT_OCT                   = 0x70;
    const T_DIGIT_DEC                   = 0x71;
    const T_QUESTION                    = 0x72;
    const T_CAPITAL_P                   = 0x73;
    const T_LEFT_SQUARE_BRACKET         = 0x74;
    const T_BACKSLASH                   = 0x75;
    const T_RIGHT_SQUARE_BRACKET        = 0x76;
    const T_CIRCUMFLEX                  = 0x77;
    const T_SMALL_C                     = 0x78;
    const T_SMALL_O                     = 0x79;
    const T_SMALL_P                     = 0x7A;
    const T_SMALL_U                     = 0x7B;
    const T_SMALL_X                     = 0x7C;
    const T_LEFT_CURLY_BRACKET          = 0x7D;
    const T_VERTICAL_LINE               = 0x7E;
    const T_RIGHT_CURLY_BRACKET         = 0x7F;
    const T_OTHER_HEX_LETTER            = 0x80;
    const T_OTHER_ASCII_LETTER          = 0x81;
    const T_PRINTABLE_ASCII_OTHER       = 0x82;
    const T_OTHER_ASCII                 = 0x83;
    const T_NOT_ASCII                   = 0x84;

    const NT_MORE_ITEMS                 = 0x85;
    const NT_MORE_ITEMS_TAIL            = 0x86;

    const T_INVALID                     = 0xFE;
    const T_EOI                         = 0xFF;
}
