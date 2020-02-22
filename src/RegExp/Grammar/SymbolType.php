<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

/**
 * List of all available UniLex regular expression productions.
 *
 * @see doc/RegExpGrammar.md
 */
abstract class SymbolType
{
    public const NT_ROOT                    = 0x00;

    public const NT_PARTS                   = 0x01;
    public const NT_ALT_PARTS               = 0x02;
    public const NT_ALT_SEPARATOR           = 0x03;
    public const NT_PART                    = 0x04;
    public const NT_ITEM                    = 0x05;
    public const NT_ASSERT                  = 0x06;
    public const NT_ASSERT_LINE_START       = 0x07;
    public const NT_ASSERT_LINE_FINISH      = 0x08;
    public const NT_ITEM_BODY               = 0x09;
    public const NT_GROUP                   = 0x0A;
    public const NT_GROUP_START             = 0x0B;
    public const NT_GROUP_END               = 0x0C;
    public const NT_CLASS                   = 0x0D;
    public const NT_CLASS_START             = 0x0E;
    public const NT_CLASS_INVERTER          = 0x0F;
    public const NT_CLASS_BODY              = 0x10;
    public const NT_FIRST_CLASS_ITEM        = 0x11;
    public const NT_CLASS_ITEM              = 0x12;
    public const NT_FIRST_CLASS_SYMBOL      = 0x13;
    public const NT_CLASS_SYMBOL            = 0x14;
    public const NT_ESC_CLASS_SYMBOL        = 0x15;
    public const NT_ESC                     = 0x16;
    public const NT_CLASS_ESC_SEQUENCE      = 0x17;
    public const NT_UNESC_CLASS_SYMBOL      = 0x18;
    public const NT_RANGE                   = 0x19;
    public const NT_RANGE_SEPARATOR         = 0x1A;
    public const NT_SYMBOL                  = 0x1C;
    public const NT_SYMBOL_ANY              = 0x1D;
    public const NT_ESC_SYMBOL              = 0x1E;
    public const NT_ESC_SEQUENCE            = 0x1F;
    public const NT_ESC_SIMPLE              = 0x20;
    public const NT_ESC_SPECIAL             = 0x21;
    public const NT_ESC_NON_PRINTABLE       = 0x22;
    public const NT_ESC_CTL                 = 0x23;
    public const NT_ESC_CTL_MARKER          = 0x24;
    public const NT_ESC_CTL_CODE            = 0x25;
    public const NT_ESC_NUM_START           = 0x26;
    public const NT_ESC_NUM_FINISH          = 0x27;
    public const NT_ESC_OCT                 = 0x28;
    public const NT_ESC_OCT_SHORT           = 0x29;
    public const NT_ESC_OCT_SHORT_MARKER    = 0x2B;
    public const NT_ESC_OCT_LONG            = 0x2C;
    public const NT_ESC_OCT_LONG_NUM        = 0x2D;
    public const NT_ESC_OCT_LONG_MARKER     = 0x2E;
    public const NT_ESC_HEX                 = 0x2F;
    public const NT_ESC_HEX_MARKER          = 0x30;
    public const NT_ESC_HEX_NUM             = 0x31;
    public const NT_ESC_HEX_SHORT_NUM       = 0x32;
    public const NT_ESC_HEX_LONG_NUM        = 0x33;
    public const NT_ESC_UNICODE             = 0x34;
    public const NT_ESC_UNICODE_MARKER      = 0x35;
    public const NT_ESC_UNICODE_NUM         = 0x36;
    public const NT_ESC_PROP                = 0x37;
    public const NT_ESC_NOT_PROP            = 0x38;
    public const NT_ESC_PROP_MARKER         = 0x39;
    public const NT_ESC_NOT_PROP_MARKER     = 0x3A;
    public const NT_PROP                    = 0x3B;
    public const NT_PROP_SHORT              = 0x3C;
    public const NT_PROP_FULL               = 0x3D;
    public const NT_PROP_START              = 0x3E;
    public const NT_PROP_FINISH             = 0x3F;
    public const NT_PROP_NAME               = 0x40;
    public const NT_NOT_PROP_START          = 0x41;
    public const NT_NOT_PROP_FINISH         = 0x42;
    public const NT_UNESC_SYMBOL            = 0x43;
    public const NT_ITEM_QUANT              = 0x44;
    public const NT_ITEM_OPT                = 0x45;
    public const NT_ITEM_QUANT_STAR         = 0x46;
    public const NT_ITEM_QUANT_PLUS         = 0x47;
    public const NT_LIMIT                   = 0x48;
    public const NT_LIMIT_START             = 0x49;
    public const NT_LIMIT_END               = 0x4A;
    public const NT_LIMIT_SEPARATOR         = 0x4B;
    public const NT_MIN                     = 0x4C;
    public const NT_MAX                     = 0x4D;
    public const NT_OCT_DIGIT               = 0x4E;
    public const NT_OCT                     = 0x4F;
    public const NT_DEC_DIGIT               = 0x50;
    public const NT_DEC                     = 0x51;
    public const NT_HEX_DIGIT               = 0x52;
    public const NT_HEX                     = 0x53;
    public const NT_META_CHAR               = 0x54;
    public const NT_ASCII_LETTER            = 0x55;
    public const NT_PRINTABLE_ASCII         = 0x56;
    public const NT_CLASS_END               = 0x57;
    public const NT_PRINTABLE_ASCII_OTHER   = 0x58;
    public const NT_CLASS_ITEMS             = 0x59;
    public const NT_PROP_NAME_PART          = 0x5B;
    public const NT_OPT_MAX                 = 0x5C;
    public const NT_OPT_OCT                 = 0x5D;
    public const NT_OPT_DEC                 = 0x5E;
    public const NT_OPT_HEX                 = 0x5F;

    public const T_CTL_ASCII                = 0x60;
    public const T_DOLLAR                   = 0x61;
    public const T_LEFT_BRACKET             = 0x62;
    public const T_RIGHT_BRACKET            = 0x63;
    public const T_STAR                     = 0x64;
    public const T_PLUS                     = 0x65;
    public const T_COMMA                    = 0x66;
    public const T_HYPHEN                   = 0x67;
    public const T_DOT                      = 0x68;
    public const T_DIGIT_ZERO               = 0x69;
    public const T_DIGIT_OCT                = 0x70;
    public const T_DIGIT_DEC                = 0x71;
    public const T_QUESTION                 = 0x72;
    public const T_CAPITAL_P                = 0x73;
    public const T_LEFT_SQUARE_BRACKET      = 0x74;
    public const T_BACKSLASH                = 0x75;
    public const T_RIGHT_SQUARE_BRACKET     = 0x76;
    public const T_CIRCUMFLEX               = 0x77;
    public const T_SMALL_C                  = 0x78;
    public const T_SMALL_O                  = 0x79;
    public const T_SMALL_P                  = 0x7A;
    public const T_SMALL_U                  = 0x7B;
    public const T_SMALL_X                  = 0x7C;
    public const T_LEFT_CURLY_BRACKET       = 0x7D;
    public const T_VERTICAL_LINE            = 0x7E;
    public const T_RIGHT_CURLY_BRACKET      = 0x7F;
    public const T_OTHER_HEX_LETTER         = 0x80;
    public const T_OTHER_ASCII_LETTER       = 0x81;
    public const T_PRINTABLE_ASCII_OTHER    = 0x82;
    public const T_OTHER_ASCII              = 0x83;
    public const T_NOT_ASCII                = 0x84;

    public const NT_MORE_ITEMS              = 0x85;
    public const NT_MORE_ITEMS_TAIL         = 0x86;
    public const NT_ALT_PARTS_TAIL          = 0x87;
    public const NT_FIRST_INV_CLASS_ITEM    = 0x88;
    public const NT_FIRST_INV_CLASS_SYMBOL  = 0x89;
    public const NT_CLASS_ITEMS_TAIL        = 0x8A;

    public const T_INVALID                  = 0xFE;
    public const T_EOI                      = 0xFF;
}
