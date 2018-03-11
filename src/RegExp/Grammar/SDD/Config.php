<?php

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
                        ->inheritHeaderAttribute('i.alternative_node', 's.alternative_node')
                        ->createChildNode('concatenate', 's.concatenate_node', 'i.alternative_node');
                },
            ],
        ],
        SymbolType::NT_PART => [
            0 => [
                // SymbolType::NT_ITEM
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $context->inheritHeaderAttribute('i.concatenate_node', 's.concatenate_node');
                },
            ],
        ],
        SymbolType::NT_ITEM => [
            1 => [
                // SymbolType::NT_ITEM_BODY
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $context
                        ->inheritHeaderAttribute('i.concatenate_node')
                        ->createChildNode('repeat', 's.repeat_node', 'i.concatenate_node');
                },
                // SymbolType::NT_ITEM_QUANT
                1 => function (SyntaxTreeSymbolRuleContext $context) {
                    // TODO: Find out why this callback doesn't execute.
                    $context
                        ->inheritSymbolAttribute(0, 'i.repeat_node', 's.repeat_node')
                        ->createChildNode('quantity', 's.quantity_node', 'i.repeat_node');
                }
            ],
        ],
        SymbolType::NT_ITEM_BODY => [
            2 => [
                // SymbolType::NT_SYMBOL
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $context->inheritHeaderAttribute('i.repeat_node', 's.repeat_node');
                },
            ],
        ],
        SymbolType::NT_SYMBOL => [
            2 => [
                // SymbolType::NT_UNESC_SYMBOL
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $context->inheritHeaderAttribute('i.repeat_node');
                },
            ],
        ],
        SymbolType::NT_UNESC_SYMBOL => [
            14 => [
                // SymbolType::T_OTHER_HEX_LETTER
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $node = $context
                        ->inheritHeaderAttribute('i.repeat_node')
                        ->createChildNode('single_code', 's.single_code', 'i.repeat_node');
                    $code = $context
                        ->getSymbol()
                        ->getAttribute('s.code');
                    $node->setAttribute('code', $code);
                },
            ],
        ],
        SymbolType::NT_ITEM_QUANT => [
            0 => [
                // SymbolType::NT_ITEM_OPT
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $node = $context
                        ->inheritHeaderAttribute('i.quantity_node', 's.quantity_node')
                        ->getNode('i.quantity_node');
                    $node->setAttribute('min', 0);
                    $node->setAttribute('max', 1);
                    $node->setAttribute('maxInfinity', false);
                },
            ],
            1 => [
                // SymbolType::NT_ITEM_QUANT_STAR
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $node = $context
                        ->inheritHeaderAttribute('i.quantity_node', 's.quantity_node')
                        ->getNode('i.quantity_node');
                    $node->setAttribute('min', 0);
                    $node->setAttribute('max', 0);
                    $node->setAttribute('maxInfinity', true);
                },
            ],
            2 => [
                // SymbolType::NT_ITEM_QUANT_PLUS
                0 => function (SyntaxTreeSymbolRuleContext $context) {
                    $node = $context
                        ->inheritHeaderAttribute('i.quantity_node', 's.quantity_node')
                        ->getNode('i.quantity_node');
                    $node->setAttribute('min', 1);
                    $node->setAttribute('max', 0);
                    $node->setAttribute('maxInfinity', true);
                },
            ],
        ],
    ],
    RuleSetLoader::PRODUCTION_RULE_MAP_KEY => [
        SymbolType::NT_ITEM_QUANT => [
            // ε
            4 => function (SyntaxTreeProductionRuleContext $context) {
            }
        ],
    ],
    RuleSetLoader::TOKEN_RULE_MAP_KEY => [
        SymbolType::T_OTHER_HEX_LETTER => function(SyntaxTreeTokenRuleContext $context) {
            $context->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR);
        },
    ],
];