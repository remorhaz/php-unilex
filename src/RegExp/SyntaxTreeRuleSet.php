<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\LL1Parser\ParsedProduction;
use Remorhaz\UniLex\LL1Parser\ParsedSymbol;
use Remorhaz\UniLex\LL1Parser\ParsedToken;
use Remorhaz\UniLex\LL1Parser\SDD\AbstractRuleSet;
use Remorhaz\UniLex\RegExp\Grammar\SymbolType;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;

class SyntaxTreeRuleSet extends AbstractRuleSet
{

    private $tree;

    public function __construct(SyntaxTree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return callable[][][]
     */
    protected function createSymbolRuleMap(): array
    {
        return [
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
        ];
    }

    public function createTokenRuleMap(): array
    {
        return [
            SymbolType::T_OTHER_HEX_LETTER => function(SyntaxTreeTokenRuleContext $context) {
                $context->setTokenAttribute('s.code', TokenAttribute::UNICODE_CHAR);
            },
        ];
    }

    /**
     * @param ParsedProduction $production
     * @param int $symbolIndex
     * @return callable
     */
    protected function getSymbolRule(ParsedProduction $production, int $symbolIndex): callable
    {
        return function (ParsedProduction $production, int $symbolIndex) {
            $context = new SyntaxTreeSymbolRuleContext($this->tree, $production, $symbolIndex);
            parent::getSymbolRule($production, $symbolIndex)($context);
        };
    }

    protected function getTokenRule(ParsedSymbol $symbol): callable
    {
        return function (ParsedSymbol $symbol, ParsedToken $token) {
            $context = new SyntaxTreeTokenRuleContext($symbol, $token);
            parent::getTokenRule($symbol)($context);
        };
    }
}
