<?php

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\LL1Parser\SDD\RuleSetLoader;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\RegExp\SyntaxTreeProductionRuleContext;
use Remorhaz\UniLex\RegExp\SyntaxTreeSymbolRuleContext;
use Remorhaz\UniLex\RegExp\SyntaxTreeTokenRuleContext;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

return [
    RuleSetLoader::SYMBOL_RULE_MAP_KEY => [
        SymbolType::NT_ROOT => [
            0 => [
                // SymbolType::NT_PARTS
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $context->createRootNode('alternative', 's.alternative_node');
                },
            ],
        ],
        SymbolType::NT_PARTS => [
            0 => [
                // SymbolType::NT_PART
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $context
                        ->getSymbol()
                        ->setAttribute('i.concatenable_count', 0);
                    /*$context
                        ->inheritHeaderAttribute('i.alternative_node', 's.alternative_node')
                        ->createChildNode('concatenate', 's.concatenate_node', 'i.alternative_node');*/
                },
            ],
        ],
        SymbolType::NT_PART => [
            0 => [
                // SymbolType::NT_ITEM
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    //$context->inheritHeaderAttribute('i.concatenate_node', 's.concatenate_node');
                },
            ],
        ],
        SymbolType::NT_ITEM => [
            1 => [
                // SymbolType::NT_ITEM_BODY
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    /*$context
                        ->inheritHeaderAttribute('i.concatenate_node')
                        ->createChildNode('repeat', 's.repeat_node', 'i.concatenate_node');*/
                },
                // SymbolType::NT_ITEM_QUANT
                1 => function (SyntaxTreeSymbolRuleContext $context) {
                    /*$context
                        ->inheritSymbolAttribute(0, 'i.repeatable_node', 's.repeatable_node')*/
                        /*->inheritSymbolAttribute(0, 'i.repeat_node', 's.repeat_node')
                        ->createChildNode('quantity', 's.quantity_node', 'i.repeat_node')*/;
                }
            ],
        ],
        SymbolType::NT_ITEM_BODY => [
            2 => [
                // SymbolType::NT_SYMBOL
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    //$context->inheritHeaderAttribute('i.repeat_node', 's.repeat_node');
                },
            ],
        ],
        SymbolType::NT_SYMBOL => [
            2 => [
                // SymbolType::NT_UNESC_SYMBOL
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    //$context->inheritHeaderAttribute('i.repeat_node');
                },
            ],
        ],
        SymbolType::NT_UNESC_SYMBOL => [
            14 => [
                // SymbolType::T_OTHER_HEX_LETTER
                0 => function (SyntaxTreeSymbolRuleContext $context) {
/*                    $context
                        ->inheritHeaderAttribute('i.repeat_node')
                        ->createChildNode('single_code', 's.single_code', 'i.repeat_node');
                    $context
                        ->setNodeAttribute('s.single_code', 'code', 's.code');*/
                },
            ],
        ],
    ],
    RuleSetLoader::PRODUCTION_RULE_MAP_KEY => [
        /**
         * Repeatable pattern.
         */
        SymbolType::NT_ITEM => [
            // [SymbolType::NT_ASSERT]
            0 => function () {
                throw new Exception("Asserts are not implemented yet");
            },
            // [SymbolType::NT_ITEM_BODY, SymbolType::NT_ITEM_QUANT]
            1 => function (SyntaxTreeProductionRuleContext $context) {
                $repeatableNodeId = $context->getSymbolAttribute(0, 's.repeatable_node');
                $min = $context->getSymbolAttribute(1, 's.min');
                $max = $context->getSymbolAttribute(1, 's.max');
                $isMaxInfinite = $context->getSymbolAttribute(1, 's.is_max_infinite');
                $shouldNotRepeat = 1 == $min && 1 == $max && !$isMaxInfinite;
                if ($shouldNotRepeat) {
                    $context->setHeaderAttribute('s.concatenable_node', $repeatableNodeId);
                    return;
                }
                $node = $context
                    ->getTree()
                    ->createNode('repeat');
                $node->setAttribute('min', $min);
                $node->setAttribute('max', $max);
                $node->setAttribute('is_max_infinite', $isMaxInfinite);
                $repeatableNode = $context
                    ->getTree()
                    ->getNode($repeatableNodeId);
                $node->addChild($repeatableNode);
                $context->setHeaderAttribute('s.concatenable_node', $node->getId());
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
            2 => function (SyntaxTreeProductionRuleContext $context) {
                $node = $context
                    ->getTree()
                    ->createNode('symbol');
                $context
                    ->setHeaderAttribute('s.repeatable_node', $node->getId());
            },
        ],

        /**
         * Repeatable pattern quantifier.
         */
        SymbolType::NT_ITEM_QUANT => [
            // [SymbolType::NT_ITEM_OPT]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.min', 0)
                    ->setHeaderAttribute('s.max', 1)
                    ->setHeaderAttribute('s.is_max_infinite', false);
            },
            // [SymbolType::NT_ITEM_QUANT_STAR]
            1 =>  function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.min', 0)
                    ->setHeaderAttribute('s.max', 0)
                    ->setHeaderAttribute('s.is_max_infinite', true);
            },
            // [SymbolType::NT_ITEM_QUANT_PLUS]
            2 =>  function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.min', 1)
                    ->setHeaderAttribute('s.max', 0)
                    ->setHeaderAttribute('s.is_max_infinite', true);
            },
            // [SymbolType::NT_LIMIT]
            3 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.min')
                    ->copySymbolAttribute(0, 's.max')
                    ->copySymbolAttribute(0, 's.is_max_infinite');
            },
            // []
            4 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.min', 1)
                    ->setHeaderAttribute('s.max', 1)
                    ->setHeaderAttribute('s.is_max_infinite', false);
            }
        ],
        SymbolType::NT_LIMIT => [
            // [SymbolType::NT_LIMIT_START, SymbolType::NT_MIN, SymbolType::NT_OPT_MAX, SymbolType::NT_LIMIT_END]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $isMaxSet = $context
                    ->copySymbolAttribute(1, 's.min', 's.number_value')
                    ->getSymbolAttribute(2, 's.is_set');
                if ($isMaxSet) {
                    $context
                        ->copySymbolAttribute(2, 's.max', 's.number_value')
                        ->copySymbolAttribute(2, 's.is_max_infinite', 's.is_infinite');
                    return;
                }
                $context
                    ->copySymbolAttribute(1, 's.max', 's.number_value')
                    ->setHeaderAttribute('s.is_max_infinite', false);
            },
        ],
        SymbolType::NT_MIN => [
            // [SymbolType::NT_DEC]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.number_value');
            },
        ],
        SymbolType::NT_MAX => [
            // [SymbolType::NT_DEC]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.number_value')
                    ->setHeaderAttribute('s.is_infinite', false);
            },
            // []
            1 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.number_value', 0)
                    ->setHeaderAttribute('s.is_infinite', true);
            },
        ],
        SymbolType::NT_OPT_MAX => [
            // [SymbolType::NT_LIMIT_SEPARATOR, SymbolType::NT_MAX]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.is_set', true)
                    ->copySymbolAttribute(1, 's.number_value')
                    ->copySymbolAttribute(1, 's.is_infinite');
            },
            // []
            1 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->setHeaderAttribute('s.is_set', false);
            },
        ],

        /**
         * Decimal numbers.
         */
        SymbolType::NT_DEC => [
            // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $number =
                    $context->getSymbolAttribute(0, 's.dec_digit') .
                    $context->getSymbolAttribute(1, 's.dec_number_tail');
                $context->setHeaderAttribute('s.number_value', (int) $number);
            },
        ],
        SymbolType::NT_OPT_DEC => [
            // [SymbolType::NT_DEC_DIGIT, SymbolType::NT_OPT_DEC]
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $numberTail =
                    $context->getSymbolAttribute(0, 's.dec_digit') .
                    $context->getSymbolAttribute(1, 's.dec_number_tail');
                $context->setHeaderAttribute('s.dec_number_tail', $numberTail);
            },
            // []
            1 => function (SyntaxTreeProductionRuleContext $context) {
                $context->setHeaderAttribute('s.dec_number_tail', '');
            },
        ],
        SymbolType::NT_DEC_DIGIT => [
            // SymbolType::T_DIGIT_ZERO
            0 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.dec_digit');
            },
            // SymbolType::T_DIGIT_OCT
            1 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.dec_digit');
            },
            // SymbolType::T_DIGIT_DEC
            2 => function (SyntaxTreeProductionRuleContext $context) {
                $context
                    ->copySymbolAttribute(0, 's.dec_digit');
            },
        ],
    ],
    RuleSetLoader::TOKEN_RULE_MAP_KEY => [
        SymbolType::T_OTHER_HEX_LETTER => function(SyntaxTreeTokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_DIGIT_ZERO => function (SyntaxTreeTokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.oct_digit', 'digit')
                ->copyTokenAttribute('s.dec_digit', 'digit')
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_DIGIT_OCT => function (SyntaxTreeTokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.oct_digit', 'digit')
                ->copyTokenAttribute('s.dec_digit', 'digit')
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_DIGIT_DEC => function (SyntaxTreeTokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.dec_digit', 'digit')
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
        SymbolType::T_SMALL_C => function (SyntaxTreeTokenRuleContext $context) {
            $context
                ->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR)
                ->copyTokenAttribute('s.hex_digit', 'digit');
        },
    ],
];