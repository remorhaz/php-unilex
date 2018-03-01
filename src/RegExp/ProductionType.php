<?php

namespace Remorhaz\UniLex\RegExp;

/**
 * List of all available UniLex regular expression productions.
 *
 * @see doc/RegExpGrammar.md
 */
abstract class ProductionType
{
    const PARTS                     = 0x01;
    const ALT_PARTS                 = 0x02;
    const ALT_SEPARATOR             = 0x03;
    const PART                      = 0x04;
    const ITEM                      = 0x05;
    const ASSERT                    = 0x06;
    const ASSERT_LINE_START         = 0x07;
    const ASSERT_LINE_FINISH        = 0x08;
    const ITEM_BODY                 = 0x09;
    const GROUP                     = 0x0A;
    const GROUP_START               = 0x0B;
    const GROUP_END                 = 0x0C;
    const CLASS_                    = 0x0D;
    const CLASS_START               = 0x0E;
    const CLASS_INVERTOR            = 0x0F;
    const CLASS_BODY                = 0x10;
    const FIRST_CLASS_ITEM          = 0x11;
    const CLASS_ITEM                = 0x12;
    const FIRST_UNESC_CLASS_SYMBOL  = 0x13;
    const CLASS_SYMBOL              = 0x14;
    const ESC_CLASS_SYMBOL          = 0x15;
    const ESC                       = 0x16;
    const CLASS_ESC_SEQUENCE        = 0x17;
    const UNESC_CLASS_SYMBOL        = 0x18;
    const RANGE                     = 0x19;
    const RANGE_SEPARATOR           = 0x1A;
    const SYMBOL_CLASS_END          = 0x1B;
    const SYMBOL                    = 0x1C;
    const SYMBOL_ANY                = 0x1D;
    const ESC_SYMBOL                = 0x1E;
    const ESC_SEQUENCE              = 0x1F;
    const ESC_SIMPLE                = 0x20;
    const ESC_SPECIAL               = 0x21;
    const ESC_NON_PRINTABLE         = 0x22;
    const ESC_CTL                   = 0x23;
    const ESC_CTL_MARKER            = 0x24;
    const ESC_CTL_CODE              = 0x25;
    const ESC_NUM_START             = 0x26;
    const ESC_NUM_FINISH            = 0x27;
    const ESC_OCT                   = 0x28;
    const ESC_OCT_SHORT             = 0x29;
    const ESC_OCT_SHORT_NUM         = 0x2A;
    const ESC_OCT_SHORT_MARKER      = 0x2B;
    const ESC_OCT_LONG              = 0x2C;
    const ESC_OCT_LONG_NUM          = 0x2D;
    const ESC_OCT_LONG_MARKER       = 0x2E;
    const ESC_HEX                   = 0x2F;
    const ESC_HEX_MARKER            = 0x30;
    const ESC_HEX_NUM               = 0x31;
    const ESC_HEX_SHORT_NUM         = 0x32;
    const ESC_HEX_LONG_NUM          = 0x33;
    const ESC_UNICODE               = 0x34;
    const ESC_UNICODE_MARKER        = 0x35;
    const ESC_UNICODE_NUM           = 0x36;
    const ESC_PROP                  = 0x37;
    const ESC_NOT_PROP              = 0x38;
    const ESC_PROP_MARKER           = 0x39;
    const ESC_NOT_PROP_MARKER       = 0x3A;
    const PROP                      = 0x3B;
    const PROP_SHORT                = 0x3C;
    const PROP_FULL                 = 0x3D;
    const PROP_START                = 0x3E;
    const PROP_FINISH               = 0x3F;
    const PROP_NAME                 = 0x40;
    const NOT_PROP_START            = 0x41;
    const NOT_PROP_FINISH           = 0x42;
    const UNESC_SYMBOL              = 0x43;
    const ITEM_QUANT                = 0x44;
    const ITEM_OPT                  = 0x45;
    const ITEM_QUANT_STAR           = 0x46;
    const ITEM_QUANT_PLUS           = 0x47;
    const LIMIT                     = 0x48;
    const LIMIT_START               = 0x49;
    const LIMIT_END                 = 0x4A;
    const LIMIT_SEPARATOR           = 0x4B;
    const MIN                       = 0x4C;
    const MAX                       = 0x4D;
    const OCT_DIGIT                 = 0x4E;
    const OCT                       = 0x4F;
    const DEC_DIGIT                 = 0x50;
    const DEC                       = 0x51;
    const HEX_DIGIT                 = 0x52;
    const HEX                       = 0x53;
    const META_CHAR                 = 0x54;
    const ASCII_LETTER              = 0x55;
    const PRINTABLE_ASCII           = 0x56;
    const CLASS_END                 = 0x57;
    const PRINTABLE_ASCII_OTHER     = 0x58;
    const CLASS_ITEMS               = 0x59;
    const ESC_OCT_SHORT_NUM_LAST    = 0x5A;
    const PROP_NAME_PART            = 0x5B;
    const OPT_MAX                   = 0x5C;
    const OPT_OCT                   = 0x5D;
    const OPT_DEC                   = 0x5E;
    const OPT_HEX                   = 0x5F;
    const EOF                       = 0xFF;
}
