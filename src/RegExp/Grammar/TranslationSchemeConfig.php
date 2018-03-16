<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

use Closure;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeLoader;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\ProductionRuleContext;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\SymbolRuleContext;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\TokenRuleContext;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

abstract class TranslationSchemeConfig
{

    public static function get()
    {
        return [
            TranslationSchemeLoader::SYMBOL_RULE_MAP_KEY => self::getSymbolActions(),
            TranslationSchemeLoader::PRODUCTION_RULE_MAP_KEY => self::getProductionActions(),
            TranslationSchemeLoader::TOKEN_RULE_MAP_KEY => self::getTerminalActions(),
        ];
    }

    private static function getSymbolActions(): array
    {
        return [
            SymbolType::NT_PARTS => [
                0 => [
                    // SymbolType::NT_ALT_PARTS
                    1 => [
                        'i.alternative_node' => self::inhSymbolAttribute(0, 's.alternative_node'),
                    ],
                ],
            ],
            SymbolType::NT_ALT_PARTS => [
                0 => [
                    1 => [
                        // SymbolType::NT_PART
                        'i.alternatives_node' => function (SymbolRuleContext $context): int {
                            return $context
                                ->createNode('alternative')
                                ->addChild($context->getNodeByHeaderAttribute('i.alternative_node'))
                                ->getId();
                        },
                    ],
                    2 => [
                        // SymbolType::NT_ALT_PARTS_TAIL
                        'i.alternatives_node' => self::inhSymbolAttribute(1, 'i.alternatives_node'),
                        'i.alternative_node' => self::inhSymbolAttribute(1, 's.alternative_node'),
                    ],
                ],
            ],
            SymbolType::NT_ALT_PARTS_TAIL => [
                0 => [
                    1 => [
                        // SymbolType::NT_PART
                        'i.alternatives_node' => function (SymbolRuleContext $context): int {
                            return $context
                                ->getNodeByHeaderAttribute('i.alternatives_node')
                                ->addChild($context->getNodeByHeaderAttribute('i.alternative_node'))
                                ->getId();
                        },
                    ],
                    2 => [
                        // SymbolType::NT_ALT_PARTS_TAIL
                        'i.alternatives_node' => self::inhSymbolAttribute(1, 'i.alternatives_node'),
                        'i.alternative_node' => self::inhSymbolAttribute(1, 's.alternative_node'),
                    ],
                ],
            ],
            SymbolType::NT_PART => [
                0 => [
                    // SymbolType::NT_MORE_ITEMS
                    1 => [
                        'i.concatenable_node' => self::inhSymbolAttribute(0, 's.concatenable_node'),
                    ],
                ],
            ],
            SymbolType::NT_MORE_ITEMS => [
                0 => [
                    // SymbolType::NT_ITEM
                    0 => [
                        'i.concatenate_node' => function (SymbolRuleContext $context): int {
                            return $context
                                ->createNode('concatenate')
                                ->addChild($context->getNodeByHeaderAttribute('i.concatenable_node'))
                                ->getId();
                        },
                    ],
                    // SymbolType::NT_MORE_ITEMS_TAIL
                    1 => [
                        'i.concatenable_node' => self::inhSymbolAttribute(0, 's.concatenable_node'),
                        'i.concatenate_node' => self::inhSymbolAttribute(0, 'i.concatenate_node'),
                    ],
                ],
            ],
            SymbolType::NT_MORE_ITEMS_TAIL => [
                0 => [
                    // SymbolType::NT_ITEM
                    0 => [
                        'i.concatenate_node' => function (SymbolRuleContext $context): int {
                            return $context
                                ->getNodeByHeaderAttribute('i.concatenate_node')
                                ->addChild($context->getNodeByHeaderAttribute('i.concatenable_node'))
                                ->getId();
                        },
                    ],
                    // SymbolType::NT_MORE_ITEMS_TAIL
                    1 => [
                        'i.concatenable_node' => self::inhSymbolAttribute(0, 's.concatenable_node'),
                        'i.concatenate_node' => self::inhSymbolAttribute(0, 'i.concatenate_node'),
                    ],
                ],
            ],
            SymbolType::NT_LIMIT => [
                0 => [
                    // SymbolType::NT_OPT_MAX
                    2 => [
                        'i.min' => self::inhSymbolAttribute(1, 's.number_value'),
                    ],
                ],
            ],
        ];
    }

    private static function getProductionActions(): array
    {
        $getSynthesizedCodeAttribute = self::synSymbolAttribute(0, 's.code');
        $getTrue = function (): bool {
            return true;
        };
        $getFalse = function (): bool {
            return false;
        };
        $getZero = function (): int {
            return 0;
        };
        $getOne = function (): int {
            return 1;
        };

        return [
            SymbolType::NT_ROOT => [
                0 => [
                    function (ProductionRuleContext $context): void {
                        $context->setRootNode($context->getNodeBySymbolAttribute(0, 's.alternatives_node'));
                    },
                ],
            ],

            /**
             * Alternative patterns.
             */
            SymbolType::NT_PARTS => [
                // [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS]
                0 => ['s.alternatives_node' => self::synSymbolAttribute(1, 's.alternatives_node')],
            ],
            SymbolType::NT_ALT_PARTS => [
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                0 => ['s.alternatives_node' => self::synSymbolAttribute(2, 's.alternatives_node')],
                // []
                1 => ['s.alternatives_node' => self::inhHeaderAttribute('i.alternative_node')],
            ],
            SymbolType::NT_ALT_PARTS_TAIL => [
                // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
                0 => ['s.alternatives_node' => self::synSymbolAttribute(2, 's.alternatives_node')],
                // []
                1 => [
                    's.alternatives_node' => function (ProductionRuleContext $context): int {
                        $context
                            ->getNodeByHeaderAttribute('i.alternatives_node')
                            ->addChild($context->getNodeByHeaderAttribute('i.alternative_node'));
                        return $context->getHeaderAttribute('i.alternatives_node');
                    }
                ],
            ],

            /**
             * Repeatable pattern.
             */
            SymbolType::NT_PART => [
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS]
                0 => ['s.alternative_node' => self::synSymbolAttribute(1, 's.concatenate_node')],
                // []
                1 => [
                    's.alternative_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('empty')
                            ->getId();
                    }
                ],
            ],
            SymbolType::NT_MORE_ITEMS => [
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                0 => ['s.concatenate_node' => self::synSymbolAttribute(1, 's.concatenate_node')],
                // []
                1 => ['s.concatenate_node' => self::inhHeaderAttribute('i.concatenable_node')],
            ],
            SymbolType::NT_MORE_ITEMS_TAIL => [
                // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
                0 => ['s.concatenate_node' => self::synSymbolAttribute(1, 's.concatenate_node')],
                // []
                1 => [
                    's.concatenate_node' => function (ProductionRuleContext $context): int {
                        $context
                            ->getNodeByHeaderAttribute('i.concatenate_node')
                            ->addChild($context->getNodeByHeaderAttribute('i.concatenable_node'));
                        return $context->getHeaderAttribute('i.concatenate_node');
                    },
                ],
            ],
            SymbolType::NT_ITEM => [
                // [SymbolType::NT_ASSERT]
                0 => ['s.concatenable_node' => self::synSymbolAttribute(0, 's.assert_node')],
                // [SymbolType::NT_ITEM_BODY, SymbolType::NT_ITEM_QUANT]
                1 => [
                    's.concatenable_node' => function (ProductionRuleContext $context): int {
                        [$min, $max, $isMaxInfinite] = $context
                            ->getSymbolAttributeList(1, 's.min', 's.max', 's.is_max_infinite');
                        $shouldNotRepeat = 1 == $min && 1 == $max && !$isMaxInfinite;
                        if ($shouldNotRepeat) {
                            return $context->getSymbolAttribute(0, 's.repeatable_node');
                        }
                        $repeatNode = $context
                            ->createNode('repeat')
                            ->setAttribute('min', $min)
                            ->setAttribute('max', $max)
                            ->setAttribute('is_max_infinite', $isMaxInfinite)
                            ->addChild($context->getNodeBySymbolAttribute(0, 's.repeatable_node'));
                        return $repeatNode->getId();
                    },
                ],
            ],
            SymbolType::NT_ITEM_BODY => [
                // [SymbolType::NT_GROUP]
                0 => ['s.repeatable_node' => self::synSymbolAttribute(0, 's.group_node')],
                // [SymbolType::NT_CLASS_]
                1 => [
                    function () {
                        throw new Exception("Symbol classes are not implemented yet");
                    },
                ],
                // [SymbolType::NT_SYMBOL]
                2 => ['s.repeatable_node' => self::synSymbolAttribute(0, 's.symbol_node')],
            ],
            SymbolType::NT_SYMBOL => [
                // [SymbolType::NT_SYMBOL_ANY]
                0 => [
                    's.symbol_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('symbol_any')
                            ->getId();
                    },
                ],
                // [SymbolType::NT_ESC_SYMBOL]
                1 => ['s.symbol_node' => self::synSymbolAttribute(0, 's.escape_node')],
                // [SymbolType::NT_UNESC_SYMBOL]
                2 => [
                    's.symbol_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('symbol')
                            ->setAttribute('code', $context->getSymbolAttribute(0, 's.code'))
                            ->getId();
                    },
                ],
            ],
            SymbolType::NT_GROUP => [
                // [SymbolType::NT_GROUP_START, SymbolType::NT_PARTS, SymbolType::NT_GROUP_END]
                0 => ['s.group_node' => self::synSymbolAttribute(1, 's.alternatives_node')],
            ],
            SymbolType::NT_ASSERT => [
                // [SymbolType::NT_ASSERT_LINE_START]
                0 => [
                    's.assert_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('assert')
                            ->setAttribute('type', 'line_start')
                            ->getId();
                    },
                ],
                // [SymbolType::NT_ASSERT_LINE_FINISH]
                1 => [
                    's.assert_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('assert')
                            ->setAttribute('type', 'line_finish')
                            ->getId();
                    },
                ],
            ],

            /**
             * Escaped patterns.
             */
            SymbolType::NT_ESC_SYMBOL => [
                // [SymbolType::NT_ESC, SymbolType::NT_ESC_SEQUENCE]
                0 => ['s.escape_node' => self::synSymbolAttribute(1, 's.escape_node')],
            ],
            SymbolType::NT_ESC_SEQUENCE => [
                // [SymbolType::NT_ESC_SIMPLE]
                0 => [
                    's.escape_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('esc_simple')
                            ->setAttribute('code', $context->getSymbolAttribute(0, 's.code'))
                            ->getId();
                    },
                ],
                // [SymbolType::NT_ESC_SPECIAL]
                1 => [
                    's.escape_node' => function (ProductionRuleContext $context): int {
                        return $context
                            ->createNode('symbol')
                            ->setAttribute('code', $context->getSymbolAttribute(0, 's.code'))
                            ->getId();
                    }
                ],
                // [SymbolType::NT_ESC_NON_PRINTABLE]
                2 => [
                    function () {
                        throw new Exception("Non-printable escapes are not implemented yet");
                    }
                ],
                // [SymbolType::NT_ESC_PROP]
                3 => [
                    function () {
                        throw new Exception("Unicode property escapes are not implemented yet");
                    }
                ],
                // [SymbolType::NT_ESC_NOT_PROP]
                4 => [
                    function () {
                        throw new Exception("Unicode non-property escapes are not implemented yet");
                    }
                ],
            ],
            SymbolType::NT_ESC_SIMPLE => [
                // [SymbolType::T_OTHER_ASCII_LETTER]
                0 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_OTHER_HEX_LETTER]
                1 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_DIGIT_OCT]
                2 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_DIGIT_DEC]
                3 => ['s.code' => $getSynthesizedCodeAttribute],
            ],
            SymbolType::NT_ESC_SPECIAL => [
                // [SymbolType::T_DOLLAR]
                0 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_LEFT_BRACKET]
                1 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_RIGHT_BRACKET]
                2 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_STAR]
                3 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_PLUS]
                4 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_COMMA]
                5 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_HYPHEN]
                6 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_QUESTION]
                7 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_LEFT_SQUARE_BRACKET]
                8 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_BACKSLASH]
                9 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_RIGHT_SQUARE_BRACKET]
                10 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_CIRCUMFLEX]
                11 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_LEFT_CURLY_BRACKET]
                12 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_VERTICAL_LINE]
                13 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_RIGHT_CURLY_BRACKET]
                14 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_CTL_ASCII]
                15 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_PRINTABLE_ASCII_OTHER]
                16 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_OTHER_ASCII]
                17 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_NOT_ASCII]
                18 => ['s.code' => $getSynthesizedCodeAttribute],
            ],

            /**
             * Repeatable pattern quantifier.
             */
            SymbolType::NT_ITEM_QUANT => [
                // [SymbolType::NT_ITEM_OPT]
                0 => [
                    's.min' => $getZero,
                    's.max' => $getOne,
                    's.is_max_infinite' => $getFalse,
                ],
                // [SymbolType::NT_ITEM_QUANT_STAR]
                1 => [
                    's.min' => $getZero,
                    's.max' => $getZero,
                    's.is_max_infinite' => $getTrue,
                ],
                // [SymbolType::NT_ITEM_QUANT_PLUS]
                2 => [
                    's.min' => $getOne,
                    's.max' => $getZero,
                    's.is_max_infinite' => $getTrue,
                ],
                // [SymbolType::NT_LIMIT]
                3 => [
                    's.min' => self::synSymbolAttribute(0, 's.min'),
                    's.max' => self::synSymbolAttribute(0, 's.max'),
                    's.is_max_infinite' => self::synSymbolAttribute(0, 's.is_max_infinite'),
                ],
                // []
                4 => [
                    's.min' => $getOne,
                    's.max' => $getOne,
                    's.is_max_infinite' => $getFalse,
                ],
            ],
            SymbolType::NT_LIMIT => [
                // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
                0 => [
                    's.min' => self::synSymbolAttribute(1, 's.number_value'),
                    's.max' => self::synSymbolAttribute(2, 's.number_value'),
                    's.is_max_infinite' => self::synSymbolAttribute(2, 's.is_infinite'),
                ],
            ],
            SymbolType::NT_MIN => [
                // [SymbolType::NT_DEC]
                0 => ['s.number_value' => self::synSymbolAttribute(0, 's.number_value')],
            ],
            SymbolType::NT_MAX => [
                // [SymbolType::NT_DEC]
                0 => [
                    's.number_value' => self::synSymbolAttribute(0, 's.number_value'),
                    's.is_infinite' => $getFalse,
                ],
                // []
                1 => [
                    's.number_value' => $getZero,
                    's.is_infinite' => $getTrue,
                ],
            ],
            SymbolType::NT_OPT_MAX => [
                // [SymbolType::NT_LIMIT_SEPARATOR, SymbolType::NT_MAX]
                0 => [
                    's.number_value' => self::synSymbolAttribute(1, 's.number_value'),
                    's.is_infinite' => self::synSymbolAttribute(1, 's.is_infinite'),
                ],
                // []
                1 => [
                    's.number_value' => self::inhHeaderAttribute('i.min'),
                    's.is_infinite' => $getFalse,
                ],
            ],

            /**
             * Decimal numbers.
             */
            SymbolType::NT_DEC => [
                // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
                0 => [
                    's.number_value' => function (ProductionRuleContext $context): int {
                        $number =
                            $context->getSymbolAttribute(0, 's.dec_digit') .
                            $context->getSymbolAttribute(1, 's.dec_number_tail');
                        return (int) $number;
                    },
                ],
            ],
            SymbolType::NT_OPT_DEC => [
                // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
                0 => [
                    's.dec_number_tail' => function (ProductionRuleContext $context): string {
                        return
                            $context->getSymbolAttribute(0, 's.dec_digit') .
                            $context->getSymbolAttribute(1, 's.dec_number_tail');
                    },
                ],
                // []
                1 => [
                    's.dec_number_tail' => function (): string {
                        return '';
                    },
                ],
            ],
            SymbolType::NT_DEC_DIGIT => [
                // SymbolType::T_DIGIT_ZERO
                0 => ['s.dec_digit' => self::synSymbolAttribute(0, 's.dec_digit')],
                // SymbolType::T_DIGIT_OCT
                1 => ['s.dec_digit' => self::synSymbolAttribute(0, 's.dec_digit')],
                // SymbolType::T_DIGIT_DEC
                2 => ['s.dec_digit' => self::synSymbolAttribute(0, 's.dec_digit')],
            ],

            /**
             * Unescaped symbols
             */
            SymbolType::NT_UNESC_SYMBOL => [
                // [SymbolType::T_COMMA]
                0 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_HYPHEN]
                1 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_DIGIT_ZERO]
                2 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_DIGIT_OCT]
                3 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_DIGIT_DEC]
                4 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_CAPITAL_P]
                5 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_RIGHT_SQUARE_BRACKET]
                6 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_SMALL_C]
                7 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_SMALL_O]
                8 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_SMALL_P]
                9 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_SMALL_U]
                10 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_SMALL_X]
                11 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_RIGHT_CURLY_BRACKET]
                12 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_CTL_ASCII]
                13 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_OTHER_HEX_LETTER]
                14 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_OTHER_ASCII_LETTER]
                15 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_PRINTABLE_ASCII_OTHER]
                16 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_OTHER_ASCII]
                17 => ['s.code' => $getSynthesizedCodeAttribute],
                // [SymbolType::T_NOT_ASCII]
                18 => ['s.code' => $getSynthesizedCodeAttribute],
            ],
        ];
    }

    private static function getTerminalActions(): array
    {
        $getTokenUnicodeChar = self::synTokenAttribute(TokenAttribute::UNICODE_CHAR);
        $getTokenDigit = self::synTokenAttribute('digit');

        return [
            SymbolType::T_OTHER_HEX_LETTER => [
                's.code' => $getTokenUnicodeChar,
                's.hex_digit' => $getTokenDigit,
            ],
            SymbolType::T_DIGIT_ZERO => [
                's.code' => $getTokenUnicodeChar,
                's.oct_digit' => $getTokenDigit,
                's.dec_digit' => $getTokenDigit,
                's.hex_digit' => $getTokenDigit,
            ],
            SymbolType::T_DIGIT_OCT => [
                's.code' => $getTokenUnicodeChar,
                's.oct_digit' => $getTokenDigit,
                's.dec_digit' => $getTokenDigit,
                's.hex_digit' => $getTokenDigit,
            ],
            SymbolType::T_DIGIT_DEC => [
                's.code' => $getTokenUnicodeChar,
                's.dec_digit' => $getTokenDigit,
                's.hex_digit' => $getTokenDigit,
            ],
            SymbolType::T_SMALL_C => [
                's.code' => $getTokenUnicodeChar,
                's.hex_digit' => $getTokenDigit,
            ],
            SymbolType::T_OTHER_ASCII_LETTER => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_COMMA => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_HYPHEN => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_CAPITAL_P => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_RIGHT_SQUARE_BRACKET => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_SMALL_O => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_SMALL_P => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_SMALL_U => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_SMALL_X => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_RIGHT_CURLY_BRACKET => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_CTL_ASCII => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_PRINTABLE_ASCII_OTHER => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_NOT_ASCII => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_DOLLAR => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_LEFT_BRACKET => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_RIGHT_BRACKET => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_STAR => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_PLUS => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_QUESTION => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_LEFT_SQUARE_BRACKET => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_BACKSLASH => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_CIRCUMFLEX => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_LEFT_CURLY_BRACKET => ['s.code' => $getTokenUnicodeChar],
            SymbolType::T_VERTICAL_LINE => ['s.code' => $getTokenUnicodeChar],
        ];
    }

    private static function inhSymbolAttribute(int $index, string $attribute): Closure
    {
        return function (SymbolRuleContext $context) use ($index, $attribute) {
            return $context->getSymbolAttribute($index, $attribute);
        };
    }

    private static function synSymbolAttribute(int $index, string $attribute): Closure
    {
        return function (ProductionRuleContext $context) use ($index, $attribute) {
            return $context->getSymbolAttribute($index, $attribute);
        };
    }

    private static function inhHeaderAttribute(string $attribute): Closure
    {
        return function (ProductionRuleContext $context) use ($attribute): int {
            return $context->getHeaderAttribute($attribute);
        };
    }

    private static function synTokenAttribute(string $attribute): Closure
    {
        return function (TokenRuleContext $context) use ($attribute) {
            return $context->getTokenAttribute($attribute);
        };
    }
}
