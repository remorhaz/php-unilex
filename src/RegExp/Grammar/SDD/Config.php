<?php

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\SDD\RuleSetLoader;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\SyntaxTree\SDD\ProductionRuleContext;
use Remorhaz\UniLex\SyntaxTree\SDD\SymbolRuleContext;
use Remorhaz\UniLex\SyntaxTree\SDD\TokenRuleContext;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

return [
    RuleSetLoader::SYMBOL_RULE_MAP_KEY => [
        SymbolType::NT_PARTS => [
            0 => [
                // SymbolType::NT_ALT_PARTS
                1 => function (SymbolRuleContext $context) {
                    $context
                        ->copySymbolAttribute(0, 'i.concatenate_node', 's.concatenate_node');
                },
            ],
        ],
        SymbolType::NT_PART => [
            0 => [
                // SymbolType::NT_MORE_ITEMS
                1 => function (SymbolRuleContext $context) {
                    $context
                        ->copySymbolAttribute(0, 'i.concatenable_node', 's.concatenable_node');
                },
            ],
        ],
        SymbolType::NT_MORE_ITEMS => [
            0 => [
                // SymbolType::NT_ITEM
                0 => function (SymbolRuleContext $context) {
                    $context
                        ->copyHeaderAttribute('i.concatenable_node')
                        ->createNode('concatenate', 'i.concatenate_node')
                        ->addChild($context->getNode('i.concatenable_node'));
                },
                // SymbolType::NT_MORE_ITEMS_TAIL
                1 => function (SymbolRuleContext $context) {
                    $context
                        ->copySymbolAttribute(0, 'i.concatenable_node', 's.concatenable_node')
                        ->copySymbolAttribute(0, 'i.concatenate_node');
                },
            ],
        ],
        SymbolType::NT_MORE_ITEMS_TAIL => [
            0 => [
                // SymbolType::NT_ITEM
                0 => function (SymbolRuleContext $context) {
                    $context
                        ->copyHeaderAttribute('i.concatenable_node')
                        ->copyHeaderAttribute('i.concatenate_node')
                        ->getNode('i.concatenate_node')
                        ->addChild($context->getNode('i.concatenable_node'));
                },
                // SymbolType::NT_MORE_ITEMS_TAIL
                1 => function (SymbolRuleContext $context) {
                    $context
                        ->copySymbolAttribute(0, 'i.concatenable_node', 's.concatenable_node')
                        ->copySymbolAttribute(0, 'i.concatenate_node');
                },
            ],
        ],
        SymbolType::NT_LIMIT => [
            0 => [
                2 => function (SymbolRuleContext $context) {
                    $context
                        ->copySymbolAttribute(1, 'i.min', 's.number_value');
                },
            ],
        ],
    ],
    RuleSetLoader::PRODUCTION_RULE_MAP_KEY => [
        SymbolType::NT_ROOT => [
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.alternative_node')
                    ->setRootNode('s.alternative_node');
            }
        ],
        SymbolType::NT_PARTS => [
            // [SymbolType::NT_PART, SymbolType::NT_ALT_PARTS]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(1, 's.alternative_node', 'i.concatenate_node');
            },
        ],
        SymbolType::NT_ALT_PARTS => [
            // [SymbolType::NT_ALT_SEPARATOR, SymbolType::NT_PARTS]
            0 => function () {
                throw new Exception("Alternatives are not implemented yet");
            },
            // []
            1 => function (ProductionRuleContext $context) {
                // TODO: Temporary hack, grammar needs modification to support alternatives
                $context
                    ->copyAttribute('s.alternative_node', 'i.concatenate_node');
            },
        ],

        /**
         * Repeatable pattern.
         */
        SymbolType::NT_PART => [
            // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(1, 's.concatenate_node');
            },
            // []
            1 => function (ProductionRuleContext $context) {
                $context
                    ->setAttribute('s.concatenate_node', null);
            },
        ],
        SymbolType::NT_MORE_ITEMS => [
            // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(1, 's.concatenate_node');
            },
            // []
            1 => function (ProductionRuleContext $context) {
                $context
                    ->copyAttribute('s.concatenate_node', 'i.concatenable_node');
            },
        ],
        SymbolType::NT_MORE_ITEMS_TAIL => [
            // [SymbolType::NT_ITEM, SymbolType::NT_MORE_ITEMS_TAIL]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(1, 's.concatenate_node');
            },
            // []
            1 => function (ProductionRuleContext $context) {
                $context
                    ->copyAttribute('s.concatenate_node', 'i.concatenate_node')
                    ->copyAttribute('s.concatenable_node', 'i.concatenable_node')
                    ->getNode('s.concatenate_node')
                    ->addChild($context->getNode('s.concatenable_node'));
            },
        ],
        SymbolType::NT_ITEM => [
            // [SymbolType::NT_ASSERT]
            0 => function () {
                throw new Exception("Asserts are not implemented yet");
            },
            // [SymbolType::NT_ITEM_BODY, SymbolType::NT_ITEM_QUANT]
            1 => function (ProductionRuleContext $context) {
                [$min, $max, $isMaxInfinite] = $context
                    ->getSymbolAttributeList(1, 's.min', 's.max', 's.is_max_infinite');
                $shouldNotRepeat = 1 == $min && 1 == $max && !$isMaxInfinite;
                if ($shouldNotRepeat) {
                    $context->copySymbolAttribute(0, 's.concatenable_node', 's.repeatable_node');
                    return;
                }
                $repeatableNode = $context->getSymbolNode(0, 's.repeatable_node');
                $context
                    ->createNode('repeat', 's.concatenable_node')
                    ->setAttribute('min', $min)
                    ->setAttribute('max', $max)
                    ->setAttribute('is_max_infinite', $isMaxInfinite)
                    ->addChild($repeatableNode);
            },
        ],
        SymbolType::NT_ITEM_BODY => [
            // [SymbolType::NT_GROUP]
            0 => function () {
                throw new Exception("Groups are not implemented yet");
            },
            // [SymbolType::NT_CLASS_]
            1 => function () {
                throw new Exception("Symbol classes are not implemented yet");
            },
            // [SymbolType::NT_SYMBOL]
            2 => function (ProductionRuleContext $context) {
                $code = $context->getSymbolAttribute(0, 's.code');
                $context
                    ->createNode('symbol', 's.repeatable_node')
                    ->setAttribute('code', $code);
            },
        ],
        SymbolType::NT_SYMBOL => [
            // [SymbolType::NT_SYMBOL_ANY]
            0 => function () {
                throw new Exception("Dot symbol is not implemented yet");
            },
            // [SymbolType::NT_ESC_SYMBOL]
            1 => function () {
                throw new Exception("Escaped symbol is not implemented yet");
            },
            // [SymbolType::NT_UNESC_SYMBOL]
            2 => function (ProductionRuleContext $context) {
                $context->copySymbolAttribute(0, 's.code');
            },
        ],

        /**
         * Repeatable pattern quantifier.
         */
        SymbolType::NT_ITEM_QUANT => [
            // [SymbolType::NT_ITEM_OPT]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->setAttribute('s.min', 0)
                    ->setAttribute('s.max', 1)
                    ->setAttribute('s.is_max_infinite', false);
            },
            // [SymbolType::NT_ITEM_QUANT_STAR]
            1 =>  function (ProductionRuleContext $context) {
                $context
                    ->setAttribute('s.min', 0)
                    ->setAttribute('s.max', 0)
                    ->setAttribute('s.is_max_infinite', true);
            },
            // [SymbolType::NT_ITEM_QUANT_PLUS]
            2 =>  function (ProductionRuleContext $context) {
                $context
                    ->setAttribute('s.min', 1)
                    ->setAttribute('s.max', 0)
                    ->setAttribute('s.is_max_infinite', true);
            },
            // [SymbolType::NT_LIMIT]
            3 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.min')
                    ->copySymbolAttribute(0, 's.max')
                    ->copySymbolAttribute(0, 's.is_max_infinite');
            },
            // []
            4 => function (ProductionRuleContext $context) {
                $context
                    ->setAttribute('s.min', 1)
                    ->setAttribute('s.max', 1)
                    ->setAttribute('s.is_max_infinite', false);
            }
        ],
        SymbolType::NT_LIMIT => [
            // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(1, 's.min', 's.number_value')
                    ->copySymbolAttribute(2, 's.max', 's.number_value')
                    ->copySymbolAttribute(2, 's.is_max_infinite', 's.is_infinite');
            },
        ],
        SymbolType::NT_MIN => [
            // [SymbolType::NT_DEC]
            0 => function (ProductionRuleContext $context) {
                $context->copySymbolAttribute(0, 's.number_value');
            },
        ],
        SymbolType::NT_MAX => [
            // [SymbolType::NT_DEC]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.number_value')
                    ->setAttribute('s.is_infinite', false);
            },
            // []
            1 => function (ProductionRuleContext $context) {
                $context
                    ->setAttribute('s.number_value', 0)
                    ->setAttribute('s.is_infinite', true);
            },
        ],
        SymbolType::NT_OPT_MAX => [
            // [SymbolType::NT_LIMIT_SEPARATOR, SymbolType::NT_MAX]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(1, 's.number_value')
                    ->copySymbolAttribute(1, 's.is_infinite');
            },
            // []
            1 => function (ProductionRuleContext $context) {
                $context
                    ->copyAttribute('s.number_value', 'i.min')
                    ->setAttribute('s.is_infinite', false);
            },
        ],

        /**
         * Decimal numbers.
         */
        SymbolType::NT_DEC => [
            // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
            0 => function (ProductionRuleContext $context) {
                $number =
                    $context->getSymbolAttribute(0, 's.dec_digit') .
                    $context->getSymbolAttribute(1, 's.dec_number_tail');
                $context->setAttribute('s.number_value', (int) $number);
            },
        ],
        SymbolType::NT_OPT_DEC => [
            // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
            0 => function (ProductionRuleContext $context) {
                $numberTail =
                    $context->getSymbolAttribute(0, 's.dec_digit') .
                    $context->getSymbolAttribute(1, 's.dec_number_tail');
                $context->setAttribute('s.dec_number_tail', $numberTail);
            },
            // []
            1 => function (ProductionRuleContext $context) {
                $context->setAttribute('s.dec_number_tail', '');
            },
        ],
        SymbolType::NT_DEC_DIGIT => [
            // SymbolType::T_DIGIT_ZERO
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.dec_digit');
            },
            // SymbolType::T_DIGIT_OCT
            1 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.dec_digit');
            },
            // SymbolType::T_DIGIT_DEC
            2 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.dec_digit');
            },
        ],
        /**
         * Raw symbols
         */
        SymbolType::NT_UNESC_SYMBOL => [
            // [SymbolType::T_COMMA]
            0 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_HYPHEN]
            1 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_DIGIT_ZERO]
            2 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_DIGIT_OCT]
            3 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_DIGIT_DEC]
            4 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_CAPITAL_P]
            5 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_RIGHT_SQUARE_BRACKET]
            6 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_SMALL_C]
            7 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_SMALL_O]
            8 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_SMALL_P]
            9 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_SMALL_U]
            10 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_SMALL_X]
            11 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_RIGHT_CURLY_BRACKET]
            12 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_CTL_ASCII]
            13 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_OTHER_HEX_LETTER]
            14 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_OTHER_ASCII_LETTER]
            15 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_PRINTABLE_ASCII_OTHER]
            16 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_OTHER_ASCII]
            17 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
            // [SymbolType::T_NOT_ASCII]
            18 => function (ProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.code');
            },
        ],
    ],
    RuleSetLoader::TOKEN_RULE_MAP_KEY => [
        SymbolType::T_OTHER_HEX_LETTER => function(TokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_DIGIT_ZERO => function (TokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.oct_digit', 'digit')
                ->copyTokenAttribute('s.dec_digit', 'digit')
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_DIGIT_OCT => function (TokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.oct_digit', 'digit')
                ->copyTokenAttribute('s.dec_digit', 'digit')
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_DIGIT_DEC => function (TokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.dec_digit', 'digit')
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_SMALL_C => function (TokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
    ],
];