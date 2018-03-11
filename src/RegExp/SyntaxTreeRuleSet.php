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
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $node = $this->createNode(
                            $production->getSymbol($symbolIndex),
                            'alternative',
                            's.alternative_node'
                        );
                        $this
                            ->tree
                            ->setRootNode($node);
                    },
                ],
            ],
            SymbolType::NT_PARTS => [
                0 => [
                    // SymbolType::NT_PART
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $production->inheritHeaderAttribute(
                            $symbolIndex,
                            'i.alternative_node',
                            's.alternative_node'
                        );
                        $this->createChildNode(
                            $production->getSymbol($symbolIndex),
                            'concatenate',
                            's.concatenate_node',
                            'i.alternative_node'
                        );
                    },
                ],
            ],
            SymbolType::NT_PART => [
                0 => [
                    // SymbolType::NT_ITEM
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $production->inheritHeaderAttribute(
                            $symbolIndex,
                            'i.concatenate_node',
                            's.concatenate_node'
                        );
                    },
                ],
            ],
            SymbolType::NT_ITEM => [
                1 => [
                    // SymbolType::NT_ITEM_BODY
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $production->inheritHeaderAttribute($symbolIndex, 'i.concatenate_node');
                        $this->createChildNode(
                            $production->getSymbol($symbolIndex),
                            'repeat',
                            's.repeat_node',
                            'i.concatenate_node'
                        );
                    },
                ],
            ],
            SymbolType::NT_ITEM_BODY => [
                2 => [
                    // SymbolType::NT_SYMBOL
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $production->inheritHeaderAttribute(
                            $symbolIndex,
                            'i.repeat_node',
                            's.repeat_node'
                        );
                    },
                ],
            ],
            SymbolType::NT_SYMBOL => [
                2 => [
                    // SymbolType::NT_UNESC_SYMBOL
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $production->inheritHeaderAttribute($symbolIndex, 'i.repeat_node');
                    },
                ],
            ],
            SymbolType::NT_UNESC_SYMBOL => [
                14 => [
                    // SymbolType::T_OTHER_HEX_LETTER
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $production->inheritHeaderAttribute($symbolIndex, 'i.repeat_node');
                        $symbol = $production->getSymbol($symbolIndex);
                        $node = $this->createChildNode(
                            $symbol,
                            'single_code',
                            's.single_code',
                            'i.repeat_node'
                        );
                        $code = $symbol->getAttribute('s.code');
                        $node->setAttribute('code', $code);
                    },
                ],
            ],
        ];
    }

    public function createTokenRuleMap(): array
    {
        return [
            SymbolType::T_OTHER_HEX_LETTER => function(ParsedSymbol $symbol, ParsedToken $token) {
                $code = $token
                    ->getToken()
                    ->getAttribute(TokenAttribute::UNICODE_CHAR);
                $symbol->setAttribute('s.code', $code);
            },
        ];
    }

    /**
     * @param ParsedSymbol $symbol
     * @param string $nodeName
     * @param string $nodeAttribute
     * @param string $parentAttribute
     * @return SyntaxTreeNode
     * @throws \Remorhaz\UniLex\Exception
     */
    private function createChildNode(
        ParsedSymbol $symbol,
        string $nodeName,
        string $nodeAttribute,
        string $parentAttribute
    ): SyntaxTreeNode {
        $node = $this->createNode($symbol, $nodeName, $nodeAttribute);
        $parentNodeId = $symbol->getAttribute($parentAttribute);
        $this
            ->tree
            ->getNode($parentNodeId)
            ->addChild($node);
        return $node;
    }

    /**
     * @param ParsedSymbol $symbol
     * @param string $nodeName
     * @param string $nodeAttribute
     * @return SyntaxTreeNode
     * @throws \Remorhaz\UniLex\Exception
     */
    private function createNode(
        ParsedSymbol $symbol,
        string $nodeName,
        string $nodeAttribute
    ): SyntaxTreeNode {
        $node = $this
            ->tree
            ->createNode($nodeName);
        $symbol->setAttribute($nodeAttribute, $node->getId());
        return $node;
    }
}
