<?php

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeLoader;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\ProductionRuleContext;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\SymbolRuleContext;
use Remorhaz\UniLex\Parser\SyntaxTree\SDD\TokenRuleContext;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

return [

    /**
     * Inherited attributes.
     */
    TranslationSchemeLoader::SYMBOL_RULE_MAP_KEY => [
        SymbolType::NT_PARTS => [
            0 => [
                // SymbolType::NT_ALT_PARTS
                1 => [
                    'i.alternative_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(0, 's.alternative_node');
                    },
                ],
            ],
        ],
        SymbolType::NT_ALT_PARTS => [
            0 => [
                1 => [
                    // SymbolType::NT_PART
                    'i.alternatives_node' => function (SymbolRuleContext $context): int {
                        $alternativesNode = $context
                            ->createNode('alternative')
                            ->addChild($context->getNodeByHeaderAttribute('i.alternative_node'));
                        return $alternativesNode->getId();
                    },
                ],
                2 => [
                    // SymbolType::NT_ALT_PARTS_TAIL
                    'i.alternatives_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(1, 'i.alternatives_node');
                    },
                    'i.alternative_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(1, 's.alternative_node');
                    },
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
                    'i.alternatives_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(1, 'i.alternatives_node');
                    },
                    'i.alternative_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(1, 's.alternative_node');
                    },
                ],
            ],
        ],
        SymbolType::NT_PART => [
            0 => [
                // SymbolType::NT_MORE_ITEMS
                1 => [
                    'i.concatenable_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(0, 's.concatenable_node');
                    },
                ],
            ],
        ],
        SymbolType::NT_MORE_ITEMS => [
            0 => [
                // SymbolType::NT_ITEM
                0 => [
                    'i.concatenate_node' => function (SymbolRuleContext $context): int {
                        $concatenateNode = $context
                            ->createNode('concatenate')
                            ->addChild($context->getNodeByHeaderAttribute('i.concatenable_node'));
                        return $concatenateNode->getId();
                    },
                ],
                // SymbolType::NT_MORE_ITEMS_TAIL
                1 => [
                    'i.concatenable_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(0, 's.concatenable_node');
                    },
                    'i.concatenate_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(0, 'i.concatenate_node');
                    },
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
                    'i.concatenable_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(0, 's.concatenable_node');
                    },
                    'i.concatenate_node' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(0, 'i.concatenate_node');
                    },
                ],
            ],
        ],
        SymbolType::NT_LIMIT => [
            0 => [
                // SymbolType::NT_OPT_MAX
                2 => [
                    'i.min' => function (SymbolRuleContext $context): int {
                        return $context->getSymbolAttribute(1, 's.number_value');
                    },
                ],
            ],
        ],
    ],

    /**
     * Synthesized attributes for non-terminals.
     */
    TranslationSchemeLoader::PRODUCTION_RULE_MAP_KEY => [
        SymbolType::NT_ROOT => [
            0 => [
                function (ProductionRuleContext $context): void {
                    $context
                        ->setRootNode($context->getNodeBySymbolAttribute(0, 's.alternatives_node'));
                },
            ],
        ],

        /**
         * Alternative patterns.
         */
        SymbolType::NT_PARTS => [
            // [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS]
            0 => [
                's.alternatives_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.alternatives_node');
                },
            ],
        ],
        SymbolType::NT_ALT_PARTS => [
            // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
            0 => [
                's.alternatives_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(2, 's.alternatives_node');
                },
            ],
            // []
            1 => [
                's.alternatives_node' => function (ProductionRuleContext $context): int {
                    return $context->getHeaderAttribute('i.alternative_node');
                },
            ],
        ],
        SymbolType::NT_ALT_PARTS_TAIL => [
            // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PART, SymbolType::NT_ALT_PARTS_TAIL]
            0 => [
                's.alternatives_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(2, 's.alternatives_node');
                }
            ],
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
            0 => [
                's.alternative_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.concatenate_node');
                },
            ],
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
            0 => [
                's.concatenate_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.concatenate_node');
                },
            ],
            // []
            1 => [
                's.concatenate_node' => function (ProductionRuleContext $context): int {
                    return $context->getHeaderAttribute('i.concatenable_node');
                },
            ],
        ],
        SymbolType::NT_MORE_ITEMS_TAIL => [
            // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
            0 => [
                's.concatenate_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.concatenate_node');
                },
            ],
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
            0 => [
                's.concatenable_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.assert_node');
                },
            ],
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
            0 => [
                's.repeatable_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.group_node');
                },
            ],
            // [SymbolType::NT_CLASS_]
            1 => [
                function () {
                    throw new Exception("Symbol classes are not implemented yet");
                },
            ],
            // [SymbolType::NT_SYMBOL]
            2 => [
                's.repeatable_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.symbol_node');
                },
            ],
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
            1 => [
                function () {
                    throw new Exception("Escaped symbol is not implemented yet");
                },
            ],
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
            0 => [
                's.group_node' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.alternatives_node');
                }
            ],
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
         * Repeatable pattern quantifier.
         */
        SymbolType::NT_ITEM_QUANT => [
            // [SymbolType::NT_ITEM_OPT]
            0 => [
                's.min' => function (): int {
                    return 0;
                },
                's.max' => function (): int {
                    return 1;
                },
                's.is_max_infinite' => function (): bool {
                    return false;
                },
            ],
            // [SymbolType::NT_ITEM_QUANT_STAR]
            1 => [
                's.min' => function (): int {
                    return 0;
                },
                's.max' => function (): int {
                    return 0;
                },
                's.is_max_infinite' => function (): bool {
                    return true;
                },
            ],
            // [SymbolType::NT_ITEM_QUANT_PLUS]
            2 => [
                's.min' => function (): int {
                    return 1;
                },
                's.max' => function (): int {
                    return 0;
                },
                's.is_max_infinite' => function (): bool {
                    return true;
                },
            ],
            // [SymbolType::NT_LIMIT]
            3 => [
                's.min' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.min');
                },
                's.max' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.max');
                },
                's.is_max_infinite' => function (ProductionRuleContext $context): bool {
                    return $context->getSymbolAttribute(0, 's.is_max_infinite');
                },
            ],
            // []
            4 => [
                's.min' => function (): int {
                    return 1;
                },
                's.max' => function (): int {
                    return 1;
                },
                's.is_max_infinite' => function (): bool {
                    return false;
                },
            ],
        ],
        SymbolType::NT_LIMIT => [
            // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
            0 => [
                's.min' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.number_value');
                },
                's.max' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(2, 's.number_value');
                },
                's.is_max_infinite' => function (ProductionRuleContext $context): bool {
                    return $context->getSymbolAttribute(2, 's.is_infinite');
                },
            ],
        ],
        SymbolType::NT_MIN => [
            // [SymbolType::NT_DEC]
            0 => [
                's.number_value' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.number_value');
                },
            ],
        ],
        SymbolType::NT_MAX => [
            // [SymbolType::NT_DEC]
            0 => [
                's.number_value' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.number_value');
                },
                's.is_infinite' => function (): bool {
                    return false;
                },
            ],
            // []
            1 => [
                's.number_value' => function (): int {
                    return 0;
                },
                's.is_infinite' => function () {
                    return true;
                },
            ],
        ],
        SymbolType::NT_OPT_MAX => [
            // [SymbolType::NT_LIMIT_SEPARATOR, SymbolType::NT_MAX]
            0 => [
                's.number_value' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(1, 's.number_value');
                },
                's.is_infinite' => function (ProductionRuleContext $context): bool {
                    return $context->getSymbolAttribute(1, 's.is_infinite');
                },
            ],
            // []
            1 => [
                's.number_value' => function (ProductionRuleContext $context): int {
                    return $context->getHeaderAttribute('i.min');
                },
                's.is_infinite' => function (): bool {
                    return false;
                },
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
            0 => [
                's.dec_digit' => function (ProductionRuleContext $context): string {
                    return $context->getSymbolAttribute(0, 's.dec_digit');
                },
            ],
            // SymbolType::T_DIGIT_OCT
            1 => [
                's.dec_digit' => function (ProductionRuleContext $context): string {
                    return $context->getSymbolAttribute(0, 's.dec_digit');
                },
            ],
            // SymbolType::T_DIGIT_DEC
            2 => [
                's.dec_digit' => function (ProductionRuleContext $context): string {
                    return $context->getSymbolAttribute(0, 's.dec_digit');
                },
            ],
        ],

        /**
         * Unescaped symbols
         */
        SymbolType::NT_UNESC_SYMBOL => [
            // [SymbolType::T_COMMA]
            0 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_HYPHEN]
            1 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_DIGIT_ZERO]
            2 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_DIGIT_OCT]
            3 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_DIGIT_DEC]
            4 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_CAPITAL_P]
            5 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_RIGHT_SQUARE_BRACKET]
            6 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_SMALL_C]
            7 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_SMALL_O]
            8 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_SMALL_P]
            9 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_SMALL_U]
            10 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_SMALL_X]
            11 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_RIGHT_CURLY_BRACKET]
            12 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_CTL_ASCII]
            13 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_OTHER_HEX_LETTER]
            14 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_OTHER_ASCII_LETTER]
            15 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_PRINTABLE_ASCII_OTHER]
            16 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_OTHER_ASCII]
            17 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
            // [SymbolType::T_NOT_ASCII]
            18 => [
                's.code' => function (ProductionRuleContext $context): int {
                    return $context->getSymbolAttribute(0, 's.code');
                },
            ],
        ],
    ],

    /**
     * Synthesized attributes for terminals.
     */
    TranslationSchemeLoader::TOKEN_RULE_MAP_KEY => [
        SymbolType::T_OTHER_HEX_LETTER => [
            's.code' => function(TokenRuleContext $context): int {
                return $context->getTokenAttribute(TokenAttribute::UNICODE_CHAR);
            },
            's.hex_digit' => function(TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
        ],
        SymbolType::T_DIGIT_ZERO => [
            's.code' => function (TokenRuleContext $context): int {
                return $context->getTokenAttribute(TokenAttribute::UNICODE_CHAR);
            },
            's.oct_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
            's.dec_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
            's.hex_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
        ],
        SymbolType::T_DIGIT_OCT => [
            's.code' => function (TokenRuleContext $context): int {
                return $context->getTokenAttribute(TokenAttribute::UNICODE_CHAR);
            },
            's.oct_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
            's.dec_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
            's.hex_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
        ],
        SymbolType::T_DIGIT_DEC => [
            's.code' => function (TokenRuleContext $context): int {
                return $context->getTokenAttribute(TokenAttribute::UNICODE_CHAR);
            },
            's.dec_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
            's.hex_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
        ],
        SymbolType::T_SMALL_C => [
            's.code' => function (TokenRuleContext $context): int {
                return $context->getTokenAttribute(TokenAttribute::UNICODE_CHAR);
            },
            's.hex_digit' => function (TokenRuleContext $context): string {
                return $context->getTokenAttribute('digit');
            },
        ],
    ],
];