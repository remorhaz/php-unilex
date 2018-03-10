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
            SymbolType::NT_ITEM_BODY => [
                2 => [
                    // SymbolType::NT_SYMBOL
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $nodeId = $this
                            ->tree
                            ->createNode('symbol')
                            ->getId();
                        $production
                            ->getSymbol($symbolIndex)
                            ->setAttribute('s.symbol_node', $nodeId);
                    },
                ],
            ],
            SymbolType::NT_SYMBOL => [
                2 => [
                    // SymbolType::NT_UNESC_SYMBOL
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $nodeId = $production
                            ->getHeader()
                            ->getAttribute('s.symbol_node');
                        $production
                            ->getSymbol($symbolIndex)
                            ->setAttribute('l.symbol_node', $nodeId);
                    },
                ],
            ],
            SymbolType::NT_UNESC_SYMBOL => [
                14 => [
                    // SymbolType::T_OTHER_HEX_LETTER
                    0 => function (ParsedProduction $production, int $symbolIndex) {
                        $nodeId = $production
                            ->getHeader()
                            ->getAttribute('l.symbol_node');
                        $symbol = $production->getSymbol($symbolIndex);
                        $symbol->setAttribute('l.symbol_node', $nodeId);
                        $filterNode = $this
                            ->tree
                            ->createNode('filter');
                        $code = $symbol->getAttribute('s.code');
                        $filterNode->setAttribute('code', $code);
                        $this
                            ->tree
                            ->getNode($nodeId)
                            ->addChild($filterNode);
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
}
