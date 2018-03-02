<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\Exception;

class TableBuilder
{

    private $grammar;

    private $first;

    private $follow;

    private $table;

    public function __construct(ContextFreeGrammar $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * @return Table
     * @throws Exception
     */
    public function getTable(): TableInterface
    {
        if (!isset($this->table)) {
            $table = new Table;
            $this->addProductionsFromNonTerminalMap($table);
            $this->table = $table;
        }
        return $this->table;
    }

    private function getFirst(): FirstInterface
    {
        if (!isset($this->first)) {
            $builder = new FirstBuilder($this->grammar);
            $this->first = $builder->getFirst();
        }
        return $this->first;
    }

    private function getFollow(): FollowInterface
    {
        if (!isset($this->follow)) {
            $builder = new FollowBuilder($this->grammar, $this->getFirst());
            $this->follow = $builder->getFollow();
        }
        return $this->follow;
    }

    /**
     * @param Table $table
     * @throws Exception
     */
    private function addProductionsFromNonTerminalMap(Table $table): void
    {
        foreach ($this->grammar->getNonTerminalMap() as $productionId => $productionList) {
            foreach ($productionList as $symbolId => $symbolIdList) {
                $this->addProductionFirsts($table, $productionId, ...$symbolIdList);
                $this->addProductionFollows($table, $productionId, ...$symbolIdList);
            }
        }
    }

    /**
     * @param Table $table
     * @param int $symbolId
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    private function addProductionFirsts(Table $table, int $symbolId, int ...$symbolIdList): void
    {
        $productionFirsts = $this->getFirst()->getProductionTokens(...$symbolIdList);
        foreach ($productionFirsts as $tokenId) {
            $table->addProduction($symbolId, $tokenId, ...$symbolIdList);
        }
    }

    /**
     * @param Table $table
     * @param int $symbolId
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    private function addProductionFollows(Table $table, int $symbolId, int ...$symbolIdList): void
    {
        if (!$this->getFirst()->productionHasEpsilon(...$symbolIdList)) {
            return;
        }
        $productionFollows = $this->getFollow()->getTokens($symbolId);
        foreach ($productionFollows as $tokenId) {
            $table->addProduction($symbolId, $tokenId, ...$symbolIdList);
        }
    }
}
