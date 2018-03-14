<?php

namespace Remorhaz\UniLex\Parser\LL1;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;
use Remorhaz\UniLex\Parser\SyntaxTree\Tree;

class ParseTreeBuilder extends AbstractParserListener
{

    private $tree;

    private $grammar;

    public function __construct(Grammar $grammar, Tree $tree)
    {
        $this->tree = $tree;
        $this->grammar = $grammar;
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $tokenNode = $this
            ->tree
            ->createNode('token', $token->getIndex())
            ->setAttribute('token', $token->getToken());
        $symbol
            ->setAttribute('s.token_node', $tokenNode->getId());
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onEoi(ParsedSymbol $symbol, ParsedToken $token): void
    {
        $tokenNode = $this
            ->tree
            ->createNode('token', $token->getIndex())
            ->setAttribute('token', $token->getToken());
        $symbol
            ->setAttribute('s.token_node', $tokenNode->getId());
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    public function onRootSymbol(ParsedSymbol $symbol): void
    {
        $node = $this
            ->tree
            ->createNode('symbol', $symbol->getIndex())
            ->setAttribute('id', $symbol->getSymbolId());
        $this
            ->tree
            ->setRootNode($node);
    }

    /**
     * @param int $symbolIndex
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function onSymbol(int $symbolIndex, ParsedProduction $production): void
    {
        $symbol = $production->getSymbol($symbolIndex);
        $node = $this
            ->tree
            ->createNode('symbol', $symbol->getIndex())
            ->setAttribute('id', $symbol->getSymbolId());
        if ($this->grammar->isTerminal($symbol->getSymbolId())) {
            $tokenNodeId = $symbol->getAttribute('s.token_node');
            $tokenNode = $this
                ->tree
                ->getNode($tokenNodeId);
            $node->addChild($tokenNode);
        }
        $parentNodeId = $production
            ->getHeader()
            ->getIndex();
        $this
            ->tree
            ->getNode($parentNodeId)
            ->addChild($node);
    }
}
