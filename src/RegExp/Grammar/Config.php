<?php

use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;

return [
    GrammarLoader::TOKEN_MAP_KEY => [
        SymbolType::T_CTL_ASCII => TokenType::CTL_ASCII,
        SymbolType::T_DOLLAR => TokenType::DOLLAR,
        SymbolType::T_LEFT_BRACKET => TokenType::LEFT_BRACKET,
        SymbolType::T_RIGHT_BRACKET => TokenType::RIGHT_BRACKET,
        SymbolType::T_STAR => TokenType::STAR,
        SymbolType::T_PLUS => TokenType::PLUS,
        SymbolType::T_COMMA => TokenType::COMMA,
        SymbolType::T_HYPHEN => TokenType::HYPHEN,
        SymbolType::T_DOT => TokenType::DOT,
        SymbolType::T_DIGIT_ZERO => TokenType::DIGIT_ZERO,
        SymbolType::T_DIGIT_OCT => TokenType::DIGIT_OCT,
        SymbolType::T_DIGIT_DEC => TokenType::DIGIT_DEC,
        SymbolType::T_QUESTION => TokenType::QUESTION,
        SymbolType::T_CAPITAL_P => TokenType::CAPITAL_P,
        SymbolType::T_LEFT_SQUARE_BRACKET => TokenType::LEFT_SQUARE_BRACKET,
        SymbolType::T_BACKSLASH => TokenType::BACKSLASH,
        SymbolType::T_RIGHT_SQUARE_BRACKET => TokenType::RIGHT_SQUARE_BRACKET,
        SymbolType::T_CIRCUMFLEX => TokenType::CIRCUMFLEX,
        SymbolType::T_SMALL_C => TokenType::SMALL_C,
        SymbolType::T_SMALL_O => TokenType::SMALL_O,
        SymbolType::T_SMALL_P => TokenType::SMALL_P,
        SymbolType::T_SMALL_U => TokenType::SMALL_U,
        SymbolType::T_SMALL_X => TokenType::SMALL_X,
        SymbolType::T_LEFT_CURLY_BRACKET => TokenType::LEFT_CURLY_BRACKET,
        SymbolType::T_VERTICAL_LINE => TokenType::VERTICAL_LINE,
        SymbolType::T_RIGHT_CURLY_BRACKET => TokenType::RIGHT_CURLY_BRACKET,
        SymbolType::T_OTHER_HEX_LETTER => TokenType::OTHER_HEX_LETTER,
        SymbolType::T_OTHER_ASCII_LETTER => TokenType::OTHER_ASCII_LETTER,
        SymbolType::T_PRINTABLE_ASCII_OTHER => TokenType::PRINTABLE_ASCII_OTHER,
        SymbolType::T_OTHER_ASCII => TokenType::OTHER_ASCII,
        SymbolType::T_NOT_ASCII => TokenType::NOT_ASCII,
        SymbolType::T_INVALID => TokenType::INVALID,
        SymbolType::T_EOI => TokenType::EOI,
    ],
    GrammarLoader::PRODUCTION_MAP_KEY => [
        SymbolType::NT_PARTS => [
            [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS],
        ],
        SymbolType::NT_ALT_PARTS => [
            [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PARTS],
            [],
        ],
        SymbolType::NT_PART => [
            [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS],
            [],
        ],
        SymbolType::NT_MORE_ITEMS => [
            [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL],
            [],
        ],
        SymbolType::NT_MORE_ITEMS_TAIL => [
            [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL],
            [],
        ],
        SymbolType::NT_ITEM => [
            [SymbolType::NT_ASSERT],
            [SymbolType::NT_ITEM_BODY, SymbolType::NT_ITEM_QUANT],
        ],
        SymbolType::NT_ASSERT => [
            [SymbolType::NT_ASSERT_LINE_START],
            [SymbolType::NT_ASSERT_LINE_FINISH],
        ],
        SymbolType::NT_ITEM_BODY => [
            [SymbolType::NT_GROUP],
            [SymbolType::NT_CLASS_],
            [SymbolType::NT_SYMBOL],
        ],
        SymbolType::NT_GROUP => [
            [SymbolType::NT_GROUP_START, SymbolType::NT_PARTS, SymbolType::NT_GROUP_END],
        ],
        SymbolType::NT_CLASS_ => [
            [SymbolType::NT_CLASS_START, SymbolType::NT_CLASS_BODY, SymbolType::NT_CLASS_END],
        ],
        SymbolType::NT_CLASS_BODY => [
            [SymbolType::NT_CLASS_INVERTOR, SymbolType::NT_FIRST_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS],
            [SymbolType::NT_FIRST_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS],
        ],
        SymbolType::NT_CLASS_ITEMS => [
            [SymbolType::NT_CLASS_ITEM, SymbolType::NT_CLASS_ITEMS],
            [],
        ],
        SymbolType::NT_FIRST_CLASS_ITEM => [
            [SymbolType::NT_FIRST_UNESC_CLASS_SYMBOL, SymbolType::NT_RANGE],
            [SymbolType::NT_CLASS_SYMBOL, SymbolType::NT_RANGE],
        ],
        SymbolType::NT_CLASS_ITEM => [
            [SymbolType::NT_CLASS_SYMBOL, SymbolType::NT_RANGE],
        ],
        SymbolType::NT_CLASS_SYMBOL => [
            [SymbolType::NT_ESC_CLASS_SYMBOL],
            [SymbolType::NT_UNESC_CLASS_SYMBOL],
        ],
        SymbolType::NT_ESC_CLASS_SYMBOL => [
            [SymbolType::NT_ESC, SymbolType::NT_CLASS_ESC_SEQUENCE],
        ],
        SymbolType::NT_CLASS_ESC_SEQUENCE => [
            [SymbolType::NT_ESC_SEQUENCE],
        ],
        SymbolType::NT_RANGE => [
            [SymbolType::NT_RANGE_SEPARATOR, SymbolType::NT_CLASS_SYMBOL],
            [],
        ],
        SymbolType::NT_SYMBOL => [
            [SymbolType::NT_SYMBOL_ANY],
            [SymbolType::NT_ESC_SYMBOL],
            [SymbolType::NT_UNESC_SYMBOL],
        ],
        SymbolType::NT_ESC_SYMBOL => [
            [SymbolType::NT_ESC, SymbolType::NT_ESC_SEQUENCE],
        ],
        SymbolType::NT_ESC_SEQUENCE => [
            [SymbolType::NT_ESC_SIMPLE],
            [SymbolType::NT_ESC_SPECIAL],
            [SymbolType::NT_ESC_NON_PRINTABLE],
            [SymbolType::NT_ESC_PROP],
            [SymbolType::NT_ESC_NOT_PROP],
        ],
        SymbolType::NT_ESC_NON_PRINTABLE => [
            [SymbolType::NT_ESC_CTL],
            [SymbolType::NT_ESC_OCT],
            [SymbolType::NT_ESC_HEX],
            [SymbolType::NT_ESC_UNICODE],
        ],
        SymbolType::NT_ESC_CTL => [
            [SymbolType::NT_ESC_CTL_MARKER, SymbolType::NT_ESC_CTL_CODE],
        ],
        SymbolType::NT_ESC_CTL_CODE => [
            [SymbolType::NT_PRINTABLE_ASCII],
        ],
        SymbolType::NT_ESC_OCT => [
            [SymbolType::NT_ESC_OCT_SHORT],
            [SymbolType::NT_ESC_OCT_LONG],
        ],
        SymbolType::NT_ESC_OCT_SHORT => [
            [SymbolType::NT_ESC_OCT_SHORT_MARKER, SymbolType::NT_ESC_OCT_SHORT_NUM],
        ],
        SymbolType::NT_ESC_OCT_SHORT_NUM => [
            [SymbolType::NT_OCT_DIGIT, SymbolType::NT_ESC_OCT_SHORT_NUM_LAST],
            // Symbol 42 has FIRST(0)/FOLLOW conflict (ε ∈ 1): 10, 11
            //[],
        ],
        SymbolType::NT_ESC_OCT_SHORT_NUM_LAST => [
            [SymbolType::NT_OCT_DIGIT],
            // Symbol 90 has FIRST(0)/FOLLOW conflict (ε ∈ 1): 10, 11
            //[],
        ],
        SymbolType::NT_ESC_OCT_LONG => [
            [SymbolType::NT_ESC_OCT_LONG_MARKER, SymbolType::NT_ESC_OCT_LONG_NUM],
        ],
        SymbolType::NT_ESC_OCT_LONG_NUM => [
            [SymbolType::NT_ESC_NUM_START, SymbolType::NT_OCT, SymbolType::NT_ESC_NUM_FINISH],
        ],
        SymbolType::NT_ESC_HEX => [
            [SymbolType::NT_ESC_HEX_MARKER, SymbolType::NT_ESC_HEX_NUM],
        ],
        SymbolType::NT_ESC_HEX_NUM => [
            [SymbolType::NT_ESC_HEX_SHORT_NUM],
            [SymbolType::NT_ESC_HEX_LONG_NUM],
        ],
        SymbolType::NT_ESC_HEX_SHORT_NUM => [
            [SymbolType::NT_HEX_DIGIT, SymbolType::NT_HEX_DIGIT]
        ],
        SymbolType::NT_ESC_HEX_LONG_NUM => [
            [SymbolType::NT_ESC_NUM_START, SymbolType::NT_HEX, SymbolType::NT_ESC_NUM_FINISH],
        ],
        SymbolType::NT_ESC_UNICODE => [
            [SymbolType::NT_ESC_UNICODE_MARKER, SymbolType::NT_ESC_UNICODE_NUM],
        ],
        SymbolType::NT_ESC_UNICODE_NUM => [
            [
                SymbolType::NT_HEX_DIGIT,
                SymbolType::NT_HEX_DIGIT,
                SymbolType::NT_HEX_DIGIT,
                SymbolType::NT_HEX_DIGIT
            ],
        ],
        SymbolType::NT_ESC_PROP => [
            [SymbolType::NT_ESC_PROP_MARKER, SymbolType::NT_PROP],
        ],
        SymbolType::NT_ESC_NOT_PROP => [
            [SymbolType::NT_ESC_NOT_PROP_MARKER, SymbolType::NT_PROP],
        ],
        SymbolType::NT_PROP => [
            [SymbolType::NT_PROP_SHORT],
            [SymbolType::NT_PROP_FULL],
        ],
        SymbolType::NT_PROP_SHORT => [
            [SymbolType::NT_NOT_PROP_START],
        ],
        SymbolType::NT_PROP_FULL => [
            [SymbolType::NT_PROP_START, SymbolType::NT_PROP_NAME, SymbolType::NT_PROP_FINISH],
        ],
        SymbolType::NT_PROP_NAME => [
            [SymbolType::NT_PROP_NAME_PART],
        ],
        SymbolType::NT_PROP_NAME_PART => [
            [SymbolType::NT_NOT_PROP_FINISH, SymbolType::NT_PROP_NAME_PART],
            [],
        ],
        SymbolType::NT_ITEM_QUANT => [
            [SymbolType::NT_ITEM_OPT],
            [SymbolType::NT_ITEM_QUANT_STAR],
            [SymbolType::NT_ITEM_QUANT_PLUS],
            [SymbolType::NT_LIMIT],
            [],
        ],
        SymbolType::NT_LIMIT => [
            [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END],
        ],
        SymbolType::NT_OPT_MAX => [
            [SymbolType::NT_LIMIT_SEPARATOR, SymbolType::NT_MAX],
            [],
        ],
        SymbolType::NT_MIN => [
            [SymbolType::NT_DEC],
        ],
        SymbolType::NT_MAX => [
            [SymbolType::NT_DEC],
            [],
        ],
        SymbolType::NT_OCT => [
            [SymbolType::NT_OCT_DIGIT, SymbolType::NT_OPT_OCT]
        ],
        SymbolType::NT_OPT_OCT => [
            [SymbolType::NT_OCT_DIGIT, SymbolType::NT_OPT_OCT],
            [],
        ],
        SymbolType::NT_DEC => [
            [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
        ],
        SymbolType::NT_OPT_DEC => [
            [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC],
            [],
        ],
        SymbolType::NT_HEX => [
            [SymbolType::NT_HEX_DIGIT, SymbolType::NT_OPT_HEX]
        ],
        SymbolType::NT_OPT_HEX => [
            [SymbolType::NT_HEX_DIGIT, SymbolType::NT_OPT_HEX],
            [],
        ],
        SymbolType::NT_PRINTABLE_ASCII => [
            [SymbolType::NT_META_CHAR],
            [SymbolType::NT_DEC_DIGIT],
            [SymbolType::NT_ASCII_LETTER],
            [SymbolType::NT_PRINTABLE_ASCII_OTHER],
        ],
        SymbolType::NT_ALT_SEPARATOR => [
            [SymbolType::T_VERTICAL_LINE],
        ],
        SymbolType::NT_ASSERT_LINE_START => [
            [SymbolType::T_CIRCUMFLEX],
        ],
        SymbolType::NT_ASSERT_LINE_FINISH => [
            [SymbolType::T_DOLLAR],
        ],
        SymbolType::NT_GROUP_START => [
            [SymbolType::T_LEFT_BRACKET],
        ],
        SymbolType::NT_GROUP_END => [
            [SymbolType::T_RIGHT_BRACKET],
        ],
        SymbolType::NT_CLASS_START => [
            [SymbolType::T_LEFT_SQUARE_BRACKET],
        ],
        SymbolType::NT_CLASS_END => [
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
        ],
        SymbolType::NT_CLASS_INVERTOR => [
            [SymbolType::T_CIRCUMFLEX],
        ],
        SymbolType::NT_FIRST_UNESC_CLASS_SYMBOL => [
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
        ],
        SymbolType::NT_ESC => [
            [SymbolType::T_BACKSLASH],
        ],
        SymbolType::NT_UNESC_CLASS_SYMBOL => [
            [SymbolType::T_DOLLAR],
            [SymbolType::T_LEFT_BRACKET],
            [SymbolType::T_RIGHT_BRACKET],
            [SymbolType::T_STAR],
            [SymbolType::T_PLUS],
            [SymbolType::T_COMMA],
            // Symbol 25 has FIRST(0)/FOLLOW conflict (ε ∈ 1): 8
            //[SymbolType::T_HYPHEN],
            [SymbolType::T_QUESTION],
            [SymbolType::T_LEFT_SQUARE_BRACKET],
            // FIRST/FIRST conflict for symbol 16[0/1]: 18
            //[SymbolType::T_CIRCUMFLEX],
            [SymbolType::T_LEFT_CURLY_BRACKET],
            [SymbolType::T_VERTICAL_LINE],
            [SymbolType::T_RIGHT_CURLY_BRACKET],
            [SymbolType::T_CTL_ASCII],
            [SymbolType::T_OTHER_HEX_LETTER],
            [SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_PRINTABLE_ASCII_OTHER],
            [SymbolType::T_OTHER_ASCII],
            [SymbolType::T_NOT_ASCII],
        ],
        SymbolType::NT_RANGE_SEPARATOR => [
            [SymbolType::T_HYPHEN],
        ],
        SymbolType::NT_SYMBOL_ANY => [
            [SymbolType::T_DOT],
        ],
        SymbolType::NT_ESC_SIMPLE => [
            [SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_OTHER_HEX_LETTER],
            [SymbolType::T_DIGIT_OCT],
            [SymbolType::T_DIGIT_DEC],
        ],
        SymbolType::NT_ESC_SPECIAL => [
            [SymbolType::T_DOLLAR],
            [SymbolType::T_LEFT_BRACKET],
            [SymbolType::T_RIGHT_BRACKET],
            [SymbolType::T_STAR],
            [SymbolType::T_PLUS],
            [SymbolType::T_COMMA],
            [SymbolType::T_HYPHEN],
            [SymbolType::T_QUESTION],
            [SymbolType::T_LEFT_SQUARE_BRACKET],
            [SymbolType::T_BACKSLASH],
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
            [SymbolType::T_CIRCUMFLEX],
            [SymbolType::T_LEFT_CURLY_BRACKET],
            [SymbolType::T_VERTICAL_LINE],
            [SymbolType::T_RIGHT_CURLY_BRACKET],
            [SymbolType::T_CTL_ASCII],
            // FIRST/FIRST conflict for symbol 31[0/1]: 28, 27
            //[SymbolType::T_OTHER_HEX_LETTER],
            //[SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_PRINTABLE_ASCII_OTHER],
            [SymbolType::T_OTHER_ASCII],
            [SymbolType::T_NOT_ASCII],
        ],
        SymbolType::NT_ESC_CTL_MARKER => [
            [SymbolType::T_SMALL_C],
        ],
        SymbolType::NT_ESC_NUM_START => [
            [SymbolType::T_LEFT_CURLY_BRACKET],
        ],
        SymbolType::NT_ESC_NUM_FINISH => [
            [SymbolType::T_RIGHT_CURLY_BRACKET],
        ],
        SymbolType::NT_ESC_OCT_SHORT_MARKER => [
            [SymbolType::T_DIGIT_ZERO],
        ],
        SymbolType::NT_ESC_OCT_LONG_MARKER => [
            [SymbolType::T_SMALL_O],
        ],
        SymbolType::NT_ESC_HEX_MARKER => [
            [SymbolType::T_SMALL_X],
        ],
        SymbolType::NT_ESC_UNICODE_MARKER => [
            [SymbolType::T_SMALL_U],
        ],
        SymbolType::NT_ESC_PROP_MARKER => [
            [SymbolType::T_SMALL_P],
        ],
        SymbolType::NT_ESC_NOT_PROP_MARKER => [
            [SymbolType::T_CAPITAL_P],
        ],
        SymbolType::NT_PROP_START => [
            [SymbolType::T_LEFT_CURLY_BRACKET],
        ],
        SymbolType::NT_PROP_FINISH => [
            [SymbolType::T_RIGHT_CURLY_BRACKET],
        ],
        SymbolType::NT_NOT_PROP_START => [
            [SymbolType::T_DOLLAR],
            [SymbolType::T_LEFT_BRACKET],
            [SymbolType::T_RIGHT_BRACKET],
            [SymbolType::T_STAR],
            [SymbolType::T_PLUS],
            [SymbolType::T_COMMA],
            [SymbolType::T_HYPHEN],
            [SymbolType::T_DOT],
            [SymbolType::T_DIGIT_ZERO],
            [SymbolType::T_DIGIT_OCT],
            [SymbolType::T_DIGIT_DEC],
            [SymbolType::T_QUESTION],
            [SymbolType::T_CAPITAL_P],
            [SymbolType::T_LEFT_SQUARE_BRACKET],
            [SymbolType::T_BACKSLASH],
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
            [SymbolType::T_CIRCUMFLEX],
            [SymbolType::T_SMALL_C],
            [SymbolType::T_SMALL_O],
            [SymbolType::T_SMALL_P],
            [SymbolType::T_SMALL_U],
            [SymbolType::T_SMALL_X],
            [SymbolType::T_VERTICAL_LINE],
            [SymbolType::T_RIGHT_CURLY_BRACKET],
            [SymbolType::T_CTL_ASCII],
            [SymbolType::T_OTHER_HEX_LETTER],
            [SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_PRINTABLE_ASCII_OTHER],
            [SymbolType::T_OTHER_ASCII],
            [SymbolType::T_NOT_ASCII],
        ],
        SymbolType::NT_NOT_PROP_FINISH => [
            [SymbolType::T_DOLLAR],
            [SymbolType::T_LEFT_BRACKET],
            [SymbolType::T_RIGHT_BRACKET],
            [SymbolType::T_STAR],
            [SymbolType::T_PLUS],
            [SymbolType::T_COMMA],
            [SymbolType::T_HYPHEN],
            [SymbolType::T_DOT],
            [SymbolType::T_DIGIT_ZERO],
            [SymbolType::T_DIGIT_OCT],
            [SymbolType::T_DIGIT_DEC],
            [SymbolType::T_QUESTION],
            [SymbolType::T_CAPITAL_P],
            [SymbolType::T_LEFT_SQUARE_BRACKET],
            [SymbolType::T_BACKSLASH],
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
            [SymbolType::T_CIRCUMFLEX],
            [SymbolType::T_SMALL_C],
            [SymbolType::T_SMALL_O],
            [SymbolType::T_SMALL_P],
            [SymbolType::T_SMALL_U],
            [SymbolType::T_SMALL_X],
            [SymbolType::T_LEFT_CURLY_BRACKET],
            [SymbolType::T_VERTICAL_LINE],
            [SymbolType::T_CTL_ASCII],
            [SymbolType::T_OTHER_HEX_LETTER],
            [SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_PRINTABLE_ASCII_OTHER],
            [SymbolType::T_OTHER_ASCII],
            [SymbolType::T_NOT_ASCII],
        ],
        SymbolType::NT_UNESC_SYMBOL => [
            [SymbolType::T_COMMA],
            [SymbolType::T_HYPHEN],
            [SymbolType::T_DIGIT_ZERO],
            [SymbolType::T_DIGIT_OCT],
            [SymbolType::T_DIGIT_DEC],
            [SymbolType::T_CAPITAL_P],
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
            // FIRST/FIRST conflict for symbol 5[0/1]: 18
            //[SymbolType::T_CIRCUMFLEX],
            [SymbolType::T_SMALL_C],
            [SymbolType::T_SMALL_O],
            [SymbolType::T_SMALL_P],
            [SymbolType::T_SMALL_U],
            [SymbolType::T_SMALL_X],
            [SymbolType::T_RIGHT_CURLY_BRACKET],
            [SymbolType::T_CTL_ASCII],
            [SymbolType::T_OTHER_HEX_LETTER],
            [SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_PRINTABLE_ASCII_OTHER],
            [SymbolType::T_OTHER_ASCII],
            [SymbolType::T_NOT_ASCII],
        ],
        SymbolType::NT_ITEM_OPT => [
            [SymbolType::T_QUESTION],
        ],
        SymbolType::NT_ITEM_QUANT_STAR => [
            [SymbolType::T_STAR],
        ],
        SymbolType::NT_ITEM_QUANT_PLUS => [
            [SymbolType::T_PLUS],
        ],
        SymbolType::NT_LIMIT_START => [
            [SymbolType::T_LEFT_CURLY_BRACKET],
        ],
        SymbolType::NT_LIMIT_END => [
            [SymbolType::T_RIGHT_CURLY_BRACKET],
        ],
        SymbolType::NT_LIMIT_SEPARATOR => [
            [SymbolType::T_COMMA],
        ],
        SymbolType::NT_OCT_DIGIT => [
            [SymbolType::T_DIGIT_ZERO],
            [SymbolType::T_DIGIT_OCT],
        ],
        SymbolType::NT_DEC_DIGIT => [
            [SymbolType::T_DIGIT_ZERO],
            [SymbolType::T_DIGIT_OCT],
            [SymbolType::T_DIGIT_DEC],
        ],
        SymbolType::NT_HEX_DIGIT => [
            [SymbolType::T_DIGIT_ZERO],
            [SymbolType::T_DIGIT_OCT],
            [SymbolType::T_DIGIT_DEC],
            [SymbolType::T_SMALL_C],
            [SymbolType::T_OTHER_HEX_LETTER],
        ],
        SymbolType::NT_META_CHAR => [
            [SymbolType::T_DOLLAR],
            [SymbolType::T_LEFT_BRACKET],
            [SymbolType::T_RIGHT_BRACKET],
            [SymbolType::T_STAR],
            [SymbolType::T_PLUS],
            [SymbolType::T_COMMA],
            [SymbolType::T_HYPHEN],
            [SymbolType::T_DOT],
            [SymbolType::T_QUESTION],
            [SymbolType::T_LEFT_SQUARE_BRACKET],
            [SymbolType::T_BACKSLASH],
            [SymbolType::T_RIGHT_SQUARE_BRACKET],
            [SymbolType::T_CIRCUMFLEX],
            [SymbolType::T_LEFT_CURLY_BRACKET],
            [SymbolType::T_VERTICAL_LINE],
            [SymbolType::T_RIGHT_CURLY_BRACKET],
        ],
        SymbolType::NT_ASCII_LETTER => [
            [SymbolType::T_CAPITAL_P],
            [SymbolType::T_SMALL_C],
            [SymbolType::T_SMALL_O],
            [SymbolType::T_SMALL_P],
            [SymbolType::T_SMALL_U],
            [SymbolType::T_SMALL_X],
            [SymbolType::T_OTHER_ASCII_LETTER],
            [SymbolType::T_OTHER_HEX_LETTER],
        ],
        SymbolType::NT_PRINTABLE_ASCII_OTHER => [
            [SymbolType::T_PRINTABLE_ASCII_OTHER],
        ],
    ],
    GrammarLoader::ROOT_SYMBOL_KEY => SymbolType::NT_ROOT,
    GrammarLoader::START_SYMBOL_KEY => SymbolType::NT_PARTS,
    GrammarLoader::EOI_SYMBOL_KEY => SymbolType::T_EOI,
];
