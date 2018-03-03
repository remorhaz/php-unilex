<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\Exception;

class ParserTable
{

    /**
     * Map of terminal productions. Key is production ID, value is list of terminal IDs.
     *
     * @var array
     */
    private $terminalProductionMap = [
        ProductionType::ALT_SEPARATOR => [TokenType::VERTICAL_LINE],
        ProductionType::ASSERT_LINE_START => [TokenType::CIRCUMFLEX],
        ProductionType::ASSERT_LINE_FINISH => [TokenType::DOLLAR],
        ProductionType::GROUP_START => [TokenType::LEFT_BRACKET],
        ProductionType::GROUP_END => [TokenType::RIGHT_BRACKET],
        ProductionType::CLASS_START => [TokenType::LEFT_SQUARE_BRACKET],
        ProductionType::CLASS_END => [TokenType::RIGHT_SQUARE_BRACKET],
        ProductionType::CLASS_INVERTOR => [TokenType::CIRCUMFLEX],
        ProductionType::FIRST_UNESC_CLASS_SYMBOL => [TokenType::RIGHT_SQUARE_BRACKET],
        ProductionType::ESC => [TokenType::BACKSLASH],
        ProductionType::UNESC_CLASS_SYMBOL => [
            TokenType::DOLLAR,
            TokenType::LEFT_BRACKET,
            TokenType::RIGHT_BRACKET,
            TokenType::STAR,
            TokenType::PLUS,
            TokenType::COMMA,
            TokenType::HYPHEN,
            TokenType::QUESTION,
            TokenType::LEFT_SQUARE_BRACKET,
            TokenType::CIRCUMFLEX,
            TokenType::LEFT_CURLY_BRACKET,
            TokenType::VERTICAL_LINE,
            TokenType::RIGHT_CURLY_BRACKET,
            TokenType::CTL_ASCII,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::PRINTABLE_ASCII_OTHER,
            TokenType::OTHER_ASCII,
            TokenType::NOT_ASCII,
        ],
        ProductionType::RANGE_SEPARATOR => [TokenType::HYPHEN],
        ProductionType::SYMBOL_CLASS_END => [TokenType::RIGHT_SQUARE_BRACKET],
        ProductionType::SYMBOL_ANY => [TokenType::DOT],
        ProductionType::ESC_SIMPLE => [
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_HEX_LETTER,
            TokenType::DIGIT_OCT,
            TokenType::DIGIT_DEC,
        ],
        ProductionType::ESC_SPECIAL => [
            TokenType::DOLLAR,
            TokenType::LEFT_BRACKET,
            TokenType::RIGHT_BRACKET,
            TokenType::STAR,
            TokenType::PLUS,
            TokenType::COMMA,
            TokenType::HYPHEN,
            TokenType::QUESTION,
            TokenType::LEFT_SQUARE_BRACKET,
            TokenType::BACKSLASH,
            TokenType::RIGHT_SQUARE_BRACKET,
            TokenType::CIRCUMFLEX,
            TokenType::LEFT_CURLY_BRACKET,
            TokenType::VERTICAL_LINE,
            TokenType::RIGHT_CURLY_BRACKET,
            TokenType::CTL_ASCII,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::PRINTABLE_ASCII_OTHER,
            TokenType::OTHER_ASCII,
            TokenType::NOT_ASCII,
        ],
        ProductionType::ESC_CTL_MARKER => [TokenType::SMALL_C],
        ProductionType::ESC_NUM_START => [TokenType::LEFT_CURLY_BRACKET],
        ProductionType::ESC_NUM_FINISH => [TokenType::RIGHT_CURLY_BRACKET],
        ProductionType::ESC_OCT_SHORT_MARKER => [TokenType::DIGIT_ZERO],
        ProductionType::ESC_OCT_LONG_MARKER => [TokenType::SMALL_O],
        ProductionType::ESC_HEX_MARKER => [TokenType::SMALL_X],
        ProductionType::ESC_UNICODE_MARKER => [TokenType::SMALL_U],
        ProductionType::ESC_PROP_MARKER => [TokenType::SMALL_P],
        ProductionType::ESC_NOT_PROP_MARKER => [TokenType::CAPITAL_P],
        ProductionType::PROP_START => [TokenType::LEFT_CURLY_BRACKET],
        ProductionType::PROP_FINISH => [TokenType::RIGHT_CURLY_BRACKET],
        ProductionType::NOT_PROP_START => [
            TokenType::DOLLAR,
            TokenType::LEFT_BRACKET,
            TokenType::RIGHT_BRACKET,
            TokenType::STAR,
            TokenType::PLUS,
            TokenType::COMMA,
            TokenType::HYPHEN,
            TokenType::DOT,
            TokenType::DIGIT_ZERO,
            TokenType::DIGIT_OCT,
            TokenType::DIGIT_DEC,
            TokenType::QUESTION,
            TokenType::CAPITAL_P,
            TokenType::LEFT_SQUARE_BRACKET,
            TokenType::BACKSLASH,
            TokenType::RIGHT_SQUARE_BRACKET,
            TokenType::CIRCUMFLEX,
            TokenType::SMALL_C,
            TokenType::SMALL_O,
            TokenType::SMALL_P,
            TokenType::SMALL_U,
            TokenType::SMALL_X,
            TokenType::VERTICAL_LINE,
            TokenType::RIGHT_CURLY_BRACKET,
            TokenType::CTL_ASCII,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::PRINTABLE_ASCII_OTHER,
            TokenType::OTHER_ASCII,
            TokenType::NOT_ASCII,
        ],
        ProductionType::NOT_PROP_FINISH => [
            TokenType::DOLLAR,
            TokenType::LEFT_BRACKET,
            TokenType::RIGHT_BRACKET,
            TokenType::STAR,
            TokenType::PLUS,
            TokenType::COMMA,
            TokenType::HYPHEN,
            TokenType::DOT,
            TokenType::DIGIT_ZERO,
            TokenType::DIGIT_OCT,
            TokenType::DIGIT_DEC,
            TokenType::QUESTION,
            TokenType::CAPITAL_P,
            TokenType::LEFT_SQUARE_BRACKET,
            TokenType::BACKSLASH,
            TokenType::RIGHT_SQUARE_BRACKET,
            TokenType::CIRCUMFLEX,
            TokenType::SMALL_C,
            TokenType::SMALL_O,
            TokenType::SMALL_P,
            TokenType::SMALL_U,
            TokenType::SMALL_X,
            TokenType::LEFT_CURLY_BRACKET,
            TokenType::VERTICAL_LINE,
            TokenType::CTL_ASCII,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::PRINTABLE_ASCII_OTHER,
            TokenType::OTHER_ASCII,
            TokenType::NOT_ASCII,
        ],
        ProductionType::UNESC_SYMBOL => [
            TokenType::COMMA,
            TokenType::HYPHEN,
            TokenType::DIGIT_ZERO,
            TokenType::DIGIT_OCT,
            TokenType::DIGIT_DEC,
            TokenType::CAPITAL_P,
            TokenType::RIGHT_SQUARE_BRACKET,
            TokenType::CIRCUMFLEX,
            TokenType::SMALL_C,
            TokenType::SMALL_O,
            TokenType::SMALL_P,
            TokenType::SMALL_U,
            TokenType::SMALL_X,
            TokenType::RIGHT_CURLY_BRACKET,
            TokenType::CTL_ASCII,
            TokenType::OTHER_HEX_LETTER,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::PRINTABLE_ASCII_OTHER,
            TokenType::OTHER_ASCII,
            TokenType::NOT_ASCII,
        ],
        ProductionType::ITEM_OPT => [TokenType::QUESTION],
        ProductionType::ITEM_QUANT_STAR => [TokenType::STAR],
        ProductionType::ITEM_QUANT_PLUS => [TokenType::PLUS],
        ProductionType::LIMIT_START => [TokenType::LEFT_CURLY_BRACKET],
        ProductionType::LIMIT_END => [TokenType::RIGHT_CURLY_BRACKET],
        ProductionType::LIMIT_SEPARATOR => [TokenType::COMMA],
        ProductionType::OCT_DIGIT => [
            TokenType::DIGIT_ZERO,
            TokenType::DIGIT_OCT,
        ],
        ProductionType::DEC_DIGIT => [
            TokenType::DIGIT_ZERO,
            TokenType::DIGIT_OCT,
            TokenType::DIGIT_DEC,
        ],
        ProductionType::HEX_DIGIT => [
            TokenType::DIGIT_ZERO,
            TokenType::DIGIT_OCT,
            TokenType::DIGIT_DEC,
            TokenType::SMALL_C,
            TokenType::OTHER_HEX_LETTER,
        ],
        ProductionType::META_CHAR => [
            TokenType::DOLLAR,
            TokenType::LEFT_BRACKET,
            TokenType::RIGHT_BRACKET,
            TokenType::STAR,
            TokenType::PLUS,
            TokenType::COMMA,
            TokenType::HYPHEN,
            TokenType::DOT,
            TokenType::QUESTION,
            TokenType::LEFT_SQUARE_BRACKET,
            TokenType::BACKSLASH,
            TokenType::RIGHT_SQUARE_BRACKET,
            TokenType::CIRCUMFLEX,
            TokenType::LEFT_CURLY_BRACKET,
            TokenType::VERTICAL_LINE,
            TokenType::RIGHT_CURLY_BRACKET,
        ],
        ProductionType::ASCII_LETTER => [
            TokenType::CAPITAL_P,
            TokenType::SMALL_C,
            TokenType::SMALL_O,
            TokenType::SMALL_P,
            TokenType::SMALL_U,
            TokenType::SMALL_X,
            TokenType::OTHER_ASCII_LETTER,
            TokenType::OTHER_HEX_LETTER,
        ],
        ProductionType::PRINTABLE_ASCII_OTHER => [TokenType::PRINTABLE_ASCII_OTHER],
        ProductionType::EOI => [TokenType::EOI],
    ];

    /**
     * Map of non-terminal productions. Key is production ID, value is list of lists of production IDs.
     * Empty list of production IDs means ε-production.
     *
     * @var array
     */
    private $nonTerminalProductionMap = [
        ProductionType::PARTS => [
            [ProductionType::PART, ProductionType::ALT_PARTS],
        ],
        ProductionType::ALT_PARTS => [
            [ProductionType::ALT_SEPARATOR, ProductionType::PARTS],
            [],
        ],
        ProductionType::PART => [
            [ProductionType::ITEM, ProductionType::PART],
            [],
        ],
        ProductionType::ITEM => [
            [ProductionType::ASSERT],
            [ProductionType::ITEM_BODY, ProductionType::ITEM_QUANT],
        ],
        ProductionType::ASSERT => [
            [ProductionType::ASSERT_LINE_START],
            [ProductionType::ASSERT_LINE_FINISH],
        ],
        ProductionType::ITEM_BODY => [
            [ProductionType::GROUP],
            [ProductionType::CLASS_],
            [ProductionType::SYMBOL],
        ],
        ProductionType::GROUP => [
            [ProductionType::GROUP_START, ProductionType::PARTS, ProductionType::GROUP_END],
        ],
        ProductionType::CLASS_ => [
            [ProductionType::CLASS_START, ProductionType::CLASS_BODY, ProductionType::CLASS_END],
        ],
        ProductionType::CLASS_BODY => [
            [ProductionType::CLASS_INVERTOR, ProductionType::FIRST_CLASS_ITEM, ProductionType::CLASS_ITEMS],
            [ProductionType::FIRST_CLASS_ITEM, ProductionType::CLASS_ITEMS],
        ],
        ProductionType::CLASS_ITEMS => [
            [ProductionType::CLASS_ITEM, ProductionType::CLASS_ITEMS],
            [],
        ],
        ProductionType::FIRST_CLASS_ITEM => [
            [ProductionType::FIRST_UNESC_CLASS_SYMBOL, ProductionType::RANGE],
            [ProductionType::CLASS_SYMBOL, ProductionType::RANGE],
        ],
        ProductionType::CLASS_ITEM => [
            [ProductionType::CLASS_SYMBOL, ProductionType::RANGE],
        ],
        ProductionType::CLASS_SYMBOL => [
            [ProductionType::ESC_CLASS_SYMBOL],
            [ProductionType::UNESC_CLASS_SYMBOL],
        ],
        ProductionType::ESC_CLASS_SYMBOL => [
            [ProductionType::ESC, ProductionType::CLASS_ESC_SEQUENCE],
        ],
        ProductionType::CLASS_ESC_SEQUENCE => [
            [ProductionType::ESC_SEQUENCE],
        ],
        ProductionType::RANGE => [
            [ProductionType::RANGE_SEPARATOR, ProductionType::CLASS_SYMBOL],
            [],
        ],
        ProductionType::SYMBOL => [
            [ProductionType::SYMBOL_ANY],
            [ProductionType::ESC_SYMBOL],
            [ProductionType::UNESC_SYMBOL],
        ],
        ProductionType::ESC_SYMBOL => [
            [ProductionType::ESC, ProductionType::ESC_SEQUENCE],
        ],
        ProductionType::ESC_SEQUENCE => [
            [ProductionType::ESC_SIMPLE],
            [ProductionType::ESC_SPECIAL],
            [ProductionType::ESC_NON_PRINTABLE],
            [ProductionType::ESC_PROP],
            [ProductionType::ESC_NOT_PROP],
        ],
        ProductionType::ESC_NON_PRINTABLE => [
            [ProductionType::ESC_CTL],
            [ProductionType::ESC_OCT],
            [ProductionType::ESC_HEX],
            [ProductionType::ESC_UNICODE],
        ],
        ProductionType::ESC_CTL => [
            [ProductionType::ESC_CTL_MARKER, ProductionType::ESC_CTL_CODE],
        ],
        ProductionType::ESC_CTL_CODE => [
            [ProductionType::PRINTABLE_ASCII],
        ],
        ProductionType::ESC_OCT => [
            [ProductionType::ESC_OCT_SHORT],
            [ProductionType::ESC_OCT_LONG],
        ],
        ProductionType::ESC_OCT_SHORT => [
            [ProductionType::ESC_OCT_SHORT_MARKER, ProductionType::ESC_OCT_SHORT_NUM],
        ],
        ProductionType::ESC_OCT_SHORT_NUM => [
            [ProductionType::OCT_DIGIT, ProductionType::ESC_OCT_SHORT_NUM_LAST],
            [],
        ],
        ProductionType::ESC_OCT_SHORT_NUM_LAST => [
            [ProductionType::OCT_DIGIT],
            [],
        ],
        ProductionType::ESC_OCT_LONG => [
            [ProductionType::ESC_OCT_LONG_MARKER, ProductionType::ESC_OCT_LONG_NUM],
        ],
        ProductionType::ESC_OCT_LONG_NUM => [
            [ProductionType::ESC_NUM_START, ProductionType::OCT, ProductionType::ESC_NUM_FINISH],
        ],
        ProductionType::ESC_HEX => [
            [ProductionType::ESC_HEX_MARKER, ProductionType::ESC_HEX_NUM],
        ],
        ProductionType::ESC_HEX_NUM => [
            [ProductionType::ESC_HEX_SHORT_NUM],
            [ProductionType::ESC_HEX_LONG_NUM],
        ],
        ProductionType::ESC_HEX_SHORT_NUM => [
            [ProductionType::HEX_DIGIT, ProductionType::HEX_DIGIT]
        ],
        ProductionType::ESC_HEX_LONG_NUM => [
            [ProductionType::ESC_NUM_START, ProductionType::HEX, ProductionType::ESC_NUM_FINISH],
        ],
        ProductionType::ESC_UNICODE => [
            [ProductionType::ESC_UNICODE_MARKER, ProductionType::ESC_UNICODE_NUM],
        ],
        ProductionType::ESC_UNICODE_NUM => [
            [
                ProductionType::HEX_DIGIT,
                ProductionType::HEX_DIGIT,
                ProductionType::HEX_DIGIT,
                ProductionType::HEX_DIGIT
            ],
        ],
        ProductionType::ESC_PROP => [
            [ProductionType::ESC_PROP_MARKER, ProductionType::PROP],
        ],
        ProductionType::ESC_NOT_PROP => [
            [ProductionType::ESC_NOT_PROP_MARKER, ProductionType::PROP],
        ],
        ProductionType::PROP => [
            [ProductionType::PROP_SHORT],
            [ProductionType::PROP_FULL],
        ],
        ProductionType::PROP_SHORT => [
            [ProductionType::NOT_PROP_START],
        ],
        ProductionType::PROP_FULL => [
            [ProductionType::PROP_START, ProductionType::PROP_NAME, ProductionType::PROP_FINISH],
        ],
        ProductionType::PROP_NAME => [
            [ProductionType::PROP_NAME_PART],
        ],
        ProductionType::PROP_NAME_PART => [
            [ProductionType::NOT_PROP_FINISH, ProductionType::PROP_NAME_PART],
            [],
        ],
        ProductionType::ITEM_QUANT => [
            [ProductionType::ITEM_OPT],
            [ProductionType::ITEM_QUANT_STAR],
            [ProductionType::ITEM_QUANT_PLUS],
            [ProductionType::LIMIT],
            [],
        ],
        ProductionType::LIMIT => [
            [ProductionType::LIMIT_START, ProductionType::MIN, ProductionType::OPT_MAX, ProductionType::LIMIT_END],
        ],
        ProductionType::OPT_MAX => [
            [ProductionType::LIMIT_SEPARATOR, ProductionType::MAX],
            [],
        ],
        ProductionType::MIN => [
            [ProductionType::DEC],
        ],
        ProductionType::MAX => [
            [ProductionType::DEC],
        ],
        ProductionType::OCT => [
            [ProductionType::OCT_DIGIT, ProductionType::OPT_OCT]
        ],
        ProductionType::OPT_OCT => [
            [ProductionType::OCT_DIGIT, ProductionType::OPT_OCT],
            [],
        ],
        ProductionType::DEC => [
            [ProductionType::DEC_DIGIT, ProductionType::OPT_DEC]
        ],
        ProductionType::OPT_DEC => [
            [ProductionType::DEC_DIGIT, ProductionType::OPT_DEC],
            [],
        ],
        ProductionType::HEX => [
            [ProductionType::HEX_DIGIT, ProductionType::OPT_HEX]
        ],
        ProductionType::OPT_HEX => [
            [ProductionType::HEX_DIGIT, ProductionType::OPT_HEX],
            [],
        ],
        ProductionType::PRINTABLE_ASCII => [
            [ProductionType::META_CHAR],
            [ProductionType::DEC_DIGIT],
            [ProductionType::ASCII_LETTER],
            [ProductionType::PRINTABLE_ASCII_OTHER],
        ],
    ];

    private $first;

    private $firstContainsEpsilon;

    public function isTerminal(int $productionType): bool
    {
        return isset($this->terminalProductionMap[$productionType]);
    }

    public function isNonTerminal(int $productionType): bool
    {
        return isset($this->nonTerminalProductionMap[$productionType]);
    }

    /**
     * @throws Exception
     */
    public function build(): void
    {
        $this->first = [];
        $this->firstContainsEpsilon = [];
        foreach ($this->terminalProductionMap as $nonTerminalId => $terminalIdList) {
            foreach ($terminalIdList as $terminalId) {
                $this->first[$nonTerminalId][] = $terminalId;
            }
        }
        foreach ($this->nonTerminalProductionMap as $nonTerminalId => $productionList) {
            if ($this->isTerminal($nonTerminalId)) {
                throw new Exception("Production {$nonTerminalId} is a terminal");
            }
            foreach ($productionList as $production) {
                if (empty($production)) {
                    $this->firstContainsEpsilon[$nonTerminalId] = true;
                }
            }
        }
        $insertCount = 0;
        do {
            foreach ($this->nonTerminalProductionMap as $nonTerminalId => $productionList) {
                foreach ($productionList as $production) {
                    foreach ($production as $productionId) {
                        if (empty($this->first[$productionId])) {
                            continue;
                        }
                        if (empty($this->firstContainsEpsilon[$productionId])) {
                            continue;
                        }
                    }
                }
            }
        } while ($insertCount > 0);
    }
}
