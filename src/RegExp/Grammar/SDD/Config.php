<?php

use Remorhaz\UniLex\LL1Parser\SDD\RuleSetLoader;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
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
    ],
    RuleSetLoader::TOKEN_RULE_MAP_KEY => [
        SymbolType::T_OTHER_HEX_LETTER => function(SyntaxTreeTokenRuleContext $context) {
            $context->copyTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR);
        },
    ],
];