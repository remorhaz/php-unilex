<?php

use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\RegExp\Grammar\ProductionType;

return [
    GrammarLoader::TOKEN_MAP_KEY => [
        ProductionType::T_CTL_ASCII => TokenType::CTL_ASCII,
        ProductionType::T_DOLLAR => TokenType::DOLLAR,
        ProductionType::T_LEFT_BRACKET => TokenType::LEFT_BRACKET,
        ProductionType::T_RIGHT_BRACKET => TokenType::RIGHT_BRACKET,
        ProductionType::T_STAR => TokenType::STAR,
        ProductionType::T_PLUS => TokenType::PLUS,
        ProductionType::T_COMMA => TokenType::COMMA,
        ProductionType::T_HYPHEN => TokenType::HYPHEN,
        ProductionType::T_DOT => TokenType::DOT,
        ProductionType::T_DIGIT_ZERO => TokenType::DIGIT_ZERO,
        ProductionType::T_DIGIT_OCT => TokenType::DIGIT_OCT,
        ProductionType::T_DIGIT_DEC => TokenType::DIGIT_DEC,
        ProductionType::T_QUESTION => TokenType::QUESTION,
        ProductionType::T_CAPITAL_P => TokenType::CAPITAL_P,
        ProductionType::T_LEFT_SQUARE_BRACKET => TokenType::LEFT_SQUARE_BRACKET,
        ProductionType::T_BACKSLASH => TokenType::BACKSLASH,
        ProductionType::T_RIGHT_SQUARE_BRACKET => TokenType::RIGHT_SQUARE_BRACKET,
        ProductionType::T_CIRCUMFLEX => TokenType::CIRCUMFLEX,
        ProductionType::T_SMALL_C => TokenType::SMALL_C,
        ProductionType::T_SMALL_O => TokenType::SMALL_O,
        ProductionType::T_SMALL_P => TokenType::SMALL_P,
        ProductionType::T_SMALL_U => TokenType::SMALL_U,
        ProductionType::T_SMALL_X => TokenType::SMALL_X,
        ProductionType::T_LEFT_CURLY_BRACKET => TokenType::LEFT_CURLY_BRACKET,
        ProductionType::T_VERTICAL_LINE => TokenType::VERTICAL_LINE,
        ProductionType::T_RIGHT_CURLY_BRACKET => TokenType::RIGHT_CURLY_BRACKET,
        ProductionType::T_OTHER_HEX_LETTER => TokenType::OTHER_HEX_LETTER,
        ProductionType::T_OTHER_ASCII_LETTER => TokenType::OTHER_ASCII_LETTER,
        ProductionType::T_PRINTABLE_ASCII_OTHER => TokenType::PRINTABLE_ASCII_OTHER,
        ProductionType::T_OTHER_ASCII => TokenType::OTHER_ASCII,
        ProductionType::T_NOT_ASCII => TokenType::NOT_ASCII,
        ProductionType::T_INVALID => TokenType::INVALID,
        ProductionType::T_EOI => TokenType::EOI,
    ],
    GrammarLoader::PRODUCTION_MAP_KEY => [
        ProductionType::NT_PARTS => [
            [ProductionType::NT_PART, ProductionType::NT_ALT_PARTS],
        ],
        ProductionType::NT_ALT_PARTS => [
            [ProductionType::NT_ALT_SEPARATOR, ProductionType::NT_PARTS],
            [],
        ],
        ProductionType::NT_PART => [
            [ProductionType::NT_ITEM, ProductionType::NT_PART],
            [],
        ],
        ProductionType::NT_ITEM => [
            [ProductionType::NT_ASSERT],
            [ProductionType::NT_ITEM_BODY, ProductionType::NT_ITEM_QUANT],
        ],
        ProductionType::NT_ASSERT => [
            [ProductionType::NT_ASSERT_LINE_START],
            [ProductionType::NT_ASSERT_LINE_FINISH],
        ],
        ProductionType::NT_ITEM_BODY => [
            [ProductionType::NT_GROUP],
            [ProductionType::NT_CLASS_],
            [ProductionType::NT_SYMBOL],
        ],
        ProductionType::NT_GROUP => [
            [ProductionType::NT_GROUP_START, ProductionType::NT_PARTS, ProductionType::NT_GROUP_END],
        ],
        ProductionType::NT_CLASS_ => [
            [ProductionType::NT_CLASS_START, ProductionType::NT_CLASS_BODY, ProductionType::NT_CLASS_END],
        ],
        ProductionType::NT_CLASS_BODY => [
            [ProductionType::NT_CLASS_INVERTOR, ProductionType::NT_FIRST_CLASS_ITEM, ProductionType::NT_CLASS_ITEMS],
            [ProductionType::NT_FIRST_CLASS_ITEM, ProductionType::NT_CLASS_ITEMS],
        ],
        ProductionType::NT_CLASS_ITEMS => [
            [ProductionType::NT_CLASS_ITEM, ProductionType::NT_CLASS_ITEMS],
            [],
        ],
        ProductionType::NT_FIRST_CLASS_ITEM => [
            [ProductionType::NT_FIRST_UNESC_CLASS_SYMBOL, ProductionType::NT_RANGE],
            [ProductionType::NT_CLASS_SYMBOL, ProductionType::NT_RANGE],
        ],
        ProductionType::NT_CLASS_ITEM => [
            [ProductionType::NT_CLASS_SYMBOL, ProductionType::NT_RANGE],
        ],
        ProductionType::NT_CLASS_SYMBOL => [
            [ProductionType::NT_ESC_CLASS_SYMBOL],
            [ProductionType::NT_UNESC_CLASS_SYMBOL],
        ],
        ProductionType::NT_ESC_CLASS_SYMBOL => [
            [ProductionType::NT_ESC, ProductionType::NT_CLASS_ESC_SEQUENCE],
        ],
        ProductionType::NT_CLASS_ESC_SEQUENCE => [
            [ProductionType::NT_ESC_SEQUENCE],
        ],
        ProductionType::NT_RANGE => [
            [ProductionType::NT_RANGE_SEPARATOR, ProductionType::NT_CLASS_SYMBOL],
            [],
        ],
        ProductionType::NT_SYMBOL => [
            [ProductionType::NT_SYMBOL_ANY],
            [ProductionType::NT_ESC_SYMBOL],
            [ProductionType::NT_UNESC_SYMBOL],
        ],
        ProductionType::NT_ESC_SYMBOL => [
            [ProductionType::NT_ESC, ProductionType::NT_ESC_SEQUENCE],
        ],
        ProductionType::NT_ESC_SEQUENCE => [
            [ProductionType::NT_ESC_SIMPLE],
            [ProductionType::NT_ESC_SPECIAL],
            [ProductionType::NT_ESC_NON_PRINTABLE],
            [ProductionType::NT_ESC_PROP],
            [ProductionType::NT_ESC_NOT_PROP],
        ],
        ProductionType::NT_ESC_NON_PRINTABLE => [
            [ProductionType::NT_ESC_CTL],
            [ProductionType::NT_ESC_OCT],
            [ProductionType::NT_ESC_HEX],
            [ProductionType::NT_ESC_UNICODE],
        ],
        ProductionType::NT_ESC_CTL => [
            [ProductionType::NT_ESC_CTL_MARKER, ProductionType::NT_ESC_CTL_CODE],
        ],
        ProductionType::NT_ESC_CTL_CODE => [
            [ProductionType::NT_PRINTABLE_ASCII],
        ],
        ProductionType::NT_ESC_OCT => [
            [ProductionType::NT_ESC_OCT_SHORT],
            [ProductionType::NT_ESC_OCT_LONG],
        ],
        ProductionType::NT_ESC_OCT_SHORT => [
            [ProductionType::NT_ESC_OCT_SHORT_MARKER, ProductionType::NT_ESC_OCT_SHORT_NUM],
        ],
        ProductionType::NT_ESC_OCT_SHORT_NUM => [
            [ProductionType::NT_OCT_DIGIT, ProductionType::NT_ESC_OCT_SHORT_NUM_LAST],
            // Symbol 42 has FIRST(0)/FOLLOW conflict (ε ∈ 1): 10, 11
            //[],
        ],
        ProductionType::NT_ESC_OCT_SHORT_NUM_LAST => [
            [ProductionType::NT_OCT_DIGIT],
            // Symbol 90 has FIRST(0)/FOLLOW conflict (ε ∈ 1): 10, 11
            //[],
        ],
        ProductionType::NT_ESC_OCT_LONG => [
            [ProductionType::NT_ESC_OCT_LONG_MARKER, ProductionType::NT_ESC_OCT_LONG_NUM],
        ],
        ProductionType::NT_ESC_OCT_LONG_NUM => [
            [ProductionType::NT_ESC_NUM_START, ProductionType::NT_OCT, ProductionType::NT_ESC_NUM_FINISH],
        ],
        ProductionType::NT_ESC_HEX => [
            [ProductionType::NT_ESC_HEX_MARKER, ProductionType::NT_ESC_HEX_NUM],
        ],
        ProductionType::NT_ESC_HEX_NUM => [
            [ProductionType::NT_ESC_HEX_SHORT_NUM],
            [ProductionType::NT_ESC_HEX_LONG_NUM],
        ],
        ProductionType::NT_ESC_HEX_SHORT_NUM => [
            [ProductionType::NT_HEX_DIGIT, ProductionType::NT_HEX_DIGIT]
        ],
        ProductionType::NT_ESC_HEX_LONG_NUM => [
            [ProductionType::NT_ESC_NUM_START, ProductionType::NT_HEX, ProductionType::NT_ESC_NUM_FINISH],
        ],
        ProductionType::NT_ESC_UNICODE => [
            [ProductionType::NT_ESC_UNICODE_MARKER, ProductionType::NT_ESC_UNICODE_NUM],
        ],
        ProductionType::NT_ESC_UNICODE_NUM => [
            [
                ProductionType::NT_HEX_DIGIT,
                ProductionType::NT_HEX_DIGIT,
                ProductionType::NT_HEX_DIGIT,
                ProductionType::NT_HEX_DIGIT
            ],
        ],
        ProductionType::NT_ESC_PROP => [
            [ProductionType::NT_ESC_PROP_MARKER, ProductionType::NT_PROP],
        ],
        ProductionType::NT_ESC_NOT_PROP => [
            [ProductionType::NT_ESC_NOT_PROP_MARKER, ProductionType::NT_PROP],
        ],
        ProductionType::NT_PROP => [
            [ProductionType::NT_PROP_SHORT],
            [ProductionType::NT_PROP_FULL],
        ],
        ProductionType::NT_PROP_SHORT => [
            [ProductionType::NT_NOT_PROP_START],
        ],
        ProductionType::NT_PROP_FULL => [
            [ProductionType::NT_PROP_START, ProductionType::NT_PROP_NAME, ProductionType::NT_PROP_FINISH],
        ],
        ProductionType::NT_PROP_NAME => [
            [ProductionType::NT_PROP_NAME_PART],
        ],
        ProductionType::NT_PROP_NAME_PART => [
            [ProductionType::NT_NOT_PROP_FINISH, ProductionType::NT_PROP_NAME_PART],
            [],
        ],
        ProductionType::NT_ITEM_QUANT => [
            [ProductionType::NT_ITEM_OPT],
            [ProductionType::NT_ITEM_QUANT_STAR],
            [ProductionType::NT_ITEM_QUANT_PLUS],
            [ProductionType::NT_LIMIT],
            [],
        ],
        ProductionType::NT_LIMIT => [
            [ProductionType::NT_LIMIT_START, ProductionType::NT_MIN, ProductionType::NT_OPT_MAX, ProductionType::NT_LIMIT_END],
        ],
        ProductionType::NT_OPT_MAX => [
            [ProductionType::NT_LIMIT_SEPARATOR, ProductionType::NT_MAX],
            [],
        ],
        ProductionType::NT_MIN => [
            [ProductionType::NT_DEC],
        ],
        ProductionType::NT_MAX => [
            [ProductionType::NT_DEC],
        ],
        ProductionType::NT_OCT => [
            [ProductionType::NT_OCT_DIGIT, ProductionType::NT_OPT_OCT]
        ],
        ProductionType::NT_OPT_OCT => [
            [ProductionType::NT_OCT_DIGIT, ProductionType::NT_OPT_OCT],
            [],
        ],
        ProductionType::NT_DEC => [
            [ProductionType::NT_DEC_DIGIT, ProductionType::NT_OPT_DEC]
        ],
        ProductionType::NT_OPT_DEC => [
            [ProductionType::NT_DEC_DIGIT, ProductionType::NT_OPT_DEC],
            [],
        ],
        ProductionType::NT_HEX => [
            [ProductionType::NT_HEX_DIGIT, ProductionType::NT_OPT_HEX]
        ],
        ProductionType::NT_OPT_HEX => [
            [ProductionType::NT_HEX_DIGIT, ProductionType::NT_OPT_HEX],
            [],
        ],
        ProductionType::NT_PRINTABLE_ASCII => [
            [ProductionType::NT_META_CHAR],
            [ProductionType::NT_DEC_DIGIT],
            [ProductionType::NT_ASCII_LETTER],
            [ProductionType::NT_PRINTABLE_ASCII_OTHER],
        ],
        ProductionType::NT_ALT_SEPARATOR => [
            [ProductionType::T_VERTICAL_LINE],
        ],
        ProductionType::NT_ASSERT_LINE_START => [
            [ProductionType::T_CIRCUMFLEX],
        ],
        ProductionType::NT_ASSERT_LINE_FINISH => [
            [ProductionType::T_DOLLAR],
        ],
        ProductionType::NT_GROUP_START => [
            [ProductionType::T_LEFT_BRACKET],
        ],
        ProductionType::NT_GROUP_END => [
            [ProductionType::T_RIGHT_BRACKET],
        ],
        ProductionType::NT_CLASS_START => [
            [ProductionType::T_LEFT_SQUARE_BRACKET],
        ],
        ProductionType::NT_CLASS_END => [
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
        ],
        ProductionType::NT_CLASS_INVERTOR => [
            [ProductionType::T_CIRCUMFLEX],
        ],
        ProductionType::NT_FIRST_UNESC_CLASS_SYMBOL => [
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
        ],
        ProductionType::NT_ESC => [
            [ProductionType::T_BACKSLASH],
        ],
        ProductionType::NT_UNESC_CLASS_SYMBOL => [
            [ProductionType::T_DOLLAR],
            [ProductionType::T_LEFT_BRACKET],
            [ProductionType::T_RIGHT_BRACKET],
            [ProductionType::T_STAR],
            [ProductionType::T_PLUS],
            [ProductionType::T_COMMA],
            // Symbol 25 has FIRST(0)/FOLLOW conflict (ε ∈ 1): 8
            //[ProductionType::T_HYPHEN],
            [ProductionType::T_QUESTION],
            [ProductionType::T_LEFT_SQUARE_BRACKET],
            // FIRST/FIRST conflict for symbol 16[0/1]: 18
            //[ProductionType::T_CIRCUMFLEX],
            [ProductionType::T_LEFT_CURLY_BRACKET],
            [ProductionType::T_VERTICAL_LINE],
            [ProductionType::T_RIGHT_CURLY_BRACKET],
            [ProductionType::T_CTL_ASCII],
            [ProductionType::T_OTHER_HEX_LETTER],
            [ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_PRINTABLE_ASCII_OTHER],
            [ProductionType::T_OTHER_ASCII],
            [ProductionType::T_NOT_ASCII],
        ],
        ProductionType::NT_RANGE_SEPARATOR => [
            [ProductionType::T_HYPHEN],
        ],
        ProductionType::NT_SYMBOL_ANY => [
            [ProductionType::T_DOT],
        ],
        ProductionType::NT_ESC_SIMPLE => [
            [ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_OTHER_HEX_LETTER],
            [ProductionType::T_DIGIT_OCT],
            [ProductionType::T_DIGIT_DEC],
        ],
        ProductionType::NT_ESC_SPECIAL => [
            [ProductionType::T_DOLLAR],
            [ProductionType::T_LEFT_BRACKET],
            [ProductionType::T_RIGHT_BRACKET],
            [ProductionType::T_STAR],
            [ProductionType::T_PLUS],
            [ProductionType::T_COMMA],
            [ProductionType::T_HYPHEN],
            [ProductionType::T_QUESTION],
            [ProductionType::T_LEFT_SQUARE_BRACKET],
            [ProductionType::T_BACKSLASH],
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
            [ProductionType::T_CIRCUMFLEX],
            [ProductionType::T_LEFT_CURLY_BRACKET],
            [ProductionType::T_VERTICAL_LINE],
            [ProductionType::T_RIGHT_CURLY_BRACKET],
            [ProductionType::T_CTL_ASCII],
            // FIRST/FIRST conflict for symbol 31[0/1]: 28, 27
            //[ProductionType::T_OTHER_HEX_LETTER],
            //[ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_PRINTABLE_ASCII_OTHER],
            [ProductionType::T_OTHER_ASCII],
            [ProductionType::T_NOT_ASCII],
        ],
        ProductionType::NT_ESC_CTL_MARKER => [
            [ProductionType::T_SMALL_C],
        ],
        ProductionType::NT_ESC_NUM_START => [
            [ProductionType::T_LEFT_CURLY_BRACKET],
        ],
        ProductionType::NT_ESC_NUM_FINISH => [
            [ProductionType::T_RIGHT_CURLY_BRACKET],
        ],
        ProductionType::NT_ESC_OCT_SHORT_MARKER => [
            [ProductionType::T_DIGIT_ZERO],
        ],
        ProductionType::NT_ESC_OCT_LONG_MARKER => [
            [ProductionType::T_SMALL_O],
        ],
        ProductionType::NT_ESC_HEX_MARKER => [
            [ProductionType::T_SMALL_X],
        ],
        ProductionType::NT_ESC_UNICODE_MARKER => [
            [ProductionType::T_SMALL_U],
        ],
        ProductionType::NT_ESC_PROP_MARKER => [
            [ProductionType::T_SMALL_P],
        ],
        ProductionType::NT_ESC_NOT_PROP_MARKER => [
            [ProductionType::T_CAPITAL_P],
        ],
        ProductionType::NT_PROP_START => [
            [ProductionType::T_LEFT_CURLY_BRACKET],
        ],
        ProductionType::NT_PROP_FINISH => [
            [ProductionType::T_RIGHT_CURLY_BRACKET],
        ],
        ProductionType::NT_NOT_PROP_START => [
            [ProductionType::T_DOLLAR],
            [ProductionType::T_LEFT_BRACKET],
            [ProductionType::T_RIGHT_BRACKET],
            [ProductionType::T_STAR],
            [ProductionType::T_PLUS],
            [ProductionType::T_COMMA],
            [ProductionType::T_HYPHEN],
            [ProductionType::T_DOT],
            [ProductionType::T_DIGIT_ZERO],
            [ProductionType::T_DIGIT_OCT],
            [ProductionType::T_DIGIT_DEC],
            [ProductionType::T_QUESTION],
            [ProductionType::T_CAPITAL_P],
            [ProductionType::T_LEFT_SQUARE_BRACKET],
            [ProductionType::T_BACKSLASH],
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
            [ProductionType::T_CIRCUMFLEX],
            [ProductionType::T_SMALL_C],
            [ProductionType::T_SMALL_O],
            [ProductionType::T_SMALL_P],
            [ProductionType::T_SMALL_U],
            [ProductionType::T_SMALL_X],
            [ProductionType::T_VERTICAL_LINE],
            [ProductionType::T_RIGHT_CURLY_BRACKET],
            [ProductionType::T_CTL_ASCII],
            [ProductionType::T_OTHER_HEX_LETTER],
            [ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_PRINTABLE_ASCII_OTHER],
            [ProductionType::T_OTHER_ASCII],
            [ProductionType::T_NOT_ASCII],
        ],
        ProductionType::NT_NOT_PROP_FINISH => [
            [ProductionType::T_DOLLAR],
            [ProductionType::T_LEFT_BRACKET],
            [ProductionType::T_RIGHT_BRACKET],
            [ProductionType::T_STAR],
            [ProductionType::T_PLUS],
            [ProductionType::T_COMMA],
            [ProductionType::T_HYPHEN],
            [ProductionType::T_DOT],
            [ProductionType::T_DIGIT_ZERO],
            [ProductionType::T_DIGIT_OCT],
            [ProductionType::T_DIGIT_DEC],
            [ProductionType::T_QUESTION],
            [ProductionType::T_CAPITAL_P],
            [ProductionType::T_LEFT_SQUARE_BRACKET],
            [ProductionType::T_BACKSLASH],
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
            [ProductionType::T_CIRCUMFLEX],
            [ProductionType::T_SMALL_C],
            [ProductionType::T_SMALL_O],
            [ProductionType::T_SMALL_P],
            [ProductionType::T_SMALL_U],
            [ProductionType::T_SMALL_X],
            [ProductionType::T_LEFT_CURLY_BRACKET],
            [ProductionType::T_VERTICAL_LINE],
            [ProductionType::T_CTL_ASCII],
            [ProductionType::T_OTHER_HEX_LETTER],
            [ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_PRINTABLE_ASCII_OTHER],
            [ProductionType::T_OTHER_ASCII],
            [ProductionType::T_NOT_ASCII],
        ],
        ProductionType::NT_UNESC_SYMBOL => [
            [ProductionType::T_COMMA],
            [ProductionType::T_HYPHEN],
            [ProductionType::T_DIGIT_ZERO],
            [ProductionType::T_DIGIT_OCT],
            [ProductionType::T_DIGIT_DEC],
            [ProductionType::T_CAPITAL_P],
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
            // FIRST/FIRST conflict for symbol 5[0/1]: 18
            //[ProductionType::T_CIRCUMFLEX],
            [ProductionType::T_SMALL_C],
            [ProductionType::T_SMALL_O],
            [ProductionType::T_SMALL_P],
            [ProductionType::T_SMALL_U],
            [ProductionType::T_SMALL_X],
            [ProductionType::T_RIGHT_CURLY_BRACKET],
            [ProductionType::T_CTL_ASCII],
            [ProductionType::T_OTHER_HEX_LETTER],
            [ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_PRINTABLE_ASCII_OTHER],
            [ProductionType::T_OTHER_ASCII],
            [ProductionType::T_NOT_ASCII],
        ],
        ProductionType::NT_ITEM_OPT => [
            [ProductionType::T_QUESTION],
        ],
        ProductionType::NT_ITEM_QUANT_STAR => [
            [ProductionType::T_STAR],
        ],
        ProductionType::NT_ITEM_QUANT_PLUS => [
            [ProductionType::T_PLUS],
        ],
        ProductionType::NT_LIMIT_START => [
            [ProductionType::T_LEFT_CURLY_BRACKET],
        ],
        ProductionType::NT_LIMIT_END => [
            [ProductionType::T_RIGHT_CURLY_BRACKET],
        ],
        ProductionType::NT_LIMIT_SEPARATOR => [
            [ProductionType::T_COMMA],
        ],
        ProductionType::NT_OCT_DIGIT => [
            [ProductionType::T_DIGIT_ZERO],
            [ProductionType::T_DIGIT_OCT],
        ],
        ProductionType::NT_DEC_DIGIT => [
            [ProductionType::T_DIGIT_ZERO],
            [ProductionType::T_DIGIT_OCT],
            [ProductionType::T_DIGIT_DEC],
        ],
        ProductionType::NT_HEX_DIGIT => [
            [ProductionType::T_DIGIT_ZERO],
            [ProductionType::T_DIGIT_OCT],
            [ProductionType::T_DIGIT_DEC],
            [ProductionType::T_SMALL_C],
            [ProductionType::T_OTHER_HEX_LETTER],
        ],
        ProductionType::NT_META_CHAR => [
            [ProductionType::T_DOLLAR],
            [ProductionType::T_LEFT_BRACKET],
            [ProductionType::T_RIGHT_BRACKET],
            [ProductionType::T_STAR],
            [ProductionType::T_PLUS],
            [ProductionType::T_COMMA],
            [ProductionType::T_HYPHEN],
            [ProductionType::T_DOT],
            [ProductionType::T_QUESTION],
            [ProductionType::T_LEFT_SQUARE_BRACKET],
            [ProductionType::T_BACKSLASH],
            [ProductionType::T_RIGHT_SQUARE_BRACKET],
            [ProductionType::T_CIRCUMFLEX],
            [ProductionType::T_LEFT_CURLY_BRACKET],
            [ProductionType::T_VERTICAL_LINE],
            [ProductionType::T_RIGHT_CURLY_BRACKET],
        ],
        ProductionType::NT_ASCII_LETTER => [
            [ProductionType::T_CAPITAL_P],
            [ProductionType::T_SMALL_C],
            [ProductionType::T_SMALL_O],
            [ProductionType::T_SMALL_P],
            [ProductionType::T_SMALL_U],
            [ProductionType::T_SMALL_X],
            [ProductionType::T_OTHER_ASCII_LETTER],
            [ProductionType::T_OTHER_HEX_LETTER],
        ],
        ProductionType::NT_PRINTABLE_ASCII_OTHER => [
            [ProductionType::T_PRINTABLE_ASCII_OTHER],
        ],
    ],
    GrammarLoader::START_SYMBOL_KEY => ProductionType::NT_PARTS,
    GrammarLoader::EOI_SYMBOL_KEY => ProductionType::T_EOI,
];
