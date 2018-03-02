<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Grammar\ContextFreeGrammar;
use Remorhaz\UniLex\Exception;

class LookupTableBuilder
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
     * @return LookupTable
     * @throws Exception
     */
    public function getTable(): LookupTableInterface
    {
        if (!isset($this->table)) {
            $table = new LookupTable;
            $this->addProductionsFromNonTerminalMap($table);
            $this->table = $table;
        }
        return $this->table;
    }

    private function getFirst(): LookupFirstInterface
    {
        if (!isset($this->first)) {
            $builder = new LookupFirstBuilder($this->grammar);
            $this->first = $builder->getFirst();
        }
        return $this->first;
    }

    private function getFollow(): LookupFollowInterface
    {
        if (!isset($this->follow)) {
            $builder = new LookupFollowBuilder($this->grammar, $this->getFirst());
            $this->follow = $builder->getFollow();
        }
        return $this->follow;
    }

    /**
     * @param LookupTable $table
     * @throws Exception
     */
    private function addProductionsFromNonTerminalMap(LookupTable $table): void
    {
        foreach ($this->grammar->getNonTerminalMap() as $productionId => $productionList) {
            foreach ($productionList as $symbolId => $symbolIdList) {
                $this->addProductionFirsts($table, $productionId, ...$symbolIdList);
                $this->addProductionFollows($table, $productionId, ...$symbolIdList);
            }
        }
    }

    /**
     * @param LookupTable $table
     * @param int $symbolId
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    private function addProductionFirsts(LookupTable $table, int $symbolId, int ...$symbolIdList): void
    {
        $productionFirsts = $this->getFirst()->getProductionTokens(...$symbolIdList);
        foreach ($productionFirsts as $tokenId) {
            $table->addProduction($symbolId, $tokenId, ...$symbolIdList);
        }
    }

    /**
     * @param LookupTable $table
     * @param int $symbolId
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    private function addProductionFollows(LookupTable $table, int $symbolId, int ...$symbolIdList): void
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
