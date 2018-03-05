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

    /**
     * @var ParseTreeSymbolNode[]
     */
    private $nodeList = [];

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
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
        $node = new ParseTreeTokenNode($token->getToken());
        $this->getNode($symbol->getIndex())->addChild($node);
    }

    public function onSymbol(ParsedSymbol $symbol): void
    {
        switch ($symbol->getId()) {
            default:
                $this->symbolLog[] = $symbol->getId();
        }
    }

    /**
     * @param null|ParsedSymbol $symbol
     * @param ParsedSymbol[] ...$production
     * @throws Exception
     */
    public function onProduction(?ParsedSymbol $symbol, ParsedSymbol ...$production): void
    {
        foreach ($production as $productionSymbol) {
            if ($this->grammar->isEoiSymbol($productionSymbol->getId())) {
                continue;
            }
            $node = new ParseTreeSymbolNode($productionSymbol->getId());
            $this->setNode($productionSymbol->getIndex(), $node);
            if (isset($symbol)) {
                $this->getNode($symbol->getIndex())->addChild($node);
                continue;
            }
            $this->setRootNodeIndex($productionSymbol->getIndex());
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
     * @param ParseTreeSymbolNode $node
     * @throws Exception
     */
    private function setNode(int $index, ParseTreeSymbolNode $node): void
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
