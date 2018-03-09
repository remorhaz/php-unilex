<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;

class ParseTreeBuilder extends AbstractParserListener
{

    private $grammar;

    private $tokenTypeLog = [];

    private $symbolLog = [];

    private $rootNodeIndex;

    private $rootSymbolId;

    /**
     * @var ParseTreeSymbolNode[]
     */
    private $nodeList = [];

    public function __construct(Grammar $grammar, int $rootSymbolId)
    {
        $this->grammar = $grammar;
        $this->rootSymbolId = $rootSymbolId;
    }

    public function onStart(): void
    {
        unset($this->rootNodeIndex);
        $this->nodeList = [];
    }

    /**
     * @param ParsedSymbol $symbol
     * @param ParsedToken $token
     * @throws Exception
     */
    public function onToken(ParsedSymbol $symbol, ParsedToken $token): void
    {
        switch ($token->getToken()->getType()) {
            default:
                $this->tokenTypeLog[] = $token->getToken()->getType();
        }
        $node = new ParseTreeTokenNode($token);
        $this->setNode($token->getIndex(), $node);
        $this->getNode($symbol->getIndex())->addChild($node);
    }

    /**
     * @param ParsedSymbol $symbol
     * @throws Exception
     */
    public function onSymbol(ParsedSymbol $symbol): void
    {
        switch ($symbol->getSymbolId()) {
            default:
                $this->symbolLog[] = $symbol->getSymbolId();
        }
        if ($symbol->getSymbolId() == $this->rootSymbolId) {
            $node = new ParseTreeSymbolNode($symbol);
            $this->setNode($symbol->getIndex(), $node);
            $this->setRootNodeIndex($symbol->getIndex());
        }
    }

    /**
     * @param ParsedProduction $production
     * @throws Exception
     */
    public function onProduction(ParsedProduction $production): void
    {
        $parentNode = $this->getNode($production->getSymbol()->getIndex());
        foreach ($production->getSymbolList() as $productionSymbol) {
            $node = new ParseTreeSymbolNode($productionSymbol);
            $this->setNode($productionSymbol->getIndex(), $node);
            $parentNode->addChild($node);
        }
    }

    /**
     * @return ParseTreeSymbolNode
     * @throws Exception
     */
    public function getRootNode(): ParseTreeSymbolNode
    {
        if (!isset($this->rootNodeIndex)) {
            throw new Exception("Root node index is not defined");
        }
        return $this->getNode($this->rootNodeIndex);
    }

    /**
     * @param int $index
     * @return ParseTreeSymbolNode
     * @throws Exception
     */
    private function getNode(int $index): ParseTreeSymbolNode
    {
        if (!isset($this->nodeList[$index])) {
            throw new Exception("Node at index {$index} is not defined");
        }
        return $this->nodeList[$index];
    }

    /**
     * @param int $index
     * @throws Exception
     */
    private function setRootNodeIndex(int $index): void
    {
        if (isset($this->rootNodeIndex)) {
            throw new Exception("Root node index is already set");
        }
        $this->rootNodeIndex = $index;
    }

    /**
     * @param int $index
     * @param ParseTreeNodeInterface $node
     * @throws Exception
     */
    private function setNode(int $index, ParseTreeNodeInterface $node): void
    {
        if (isset($this->nodeList[$index])) {
            throw new Exception("Node at index {$index} is already set");
        }
        $this->nodeList[$index] = $node;
    }

    /**
     * @return array
     * @todo Debug method.
     */
    public function getTokenTypeLog(): array
    {
        return $this->tokenTypeLog;
    }

    /**
     * @return array
     * @todo Debug method.
     */
    public function getSymbolLog(): array
    {
        return $this->symbolLog;
    }
}
