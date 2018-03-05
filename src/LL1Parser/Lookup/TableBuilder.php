<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;

class TableBuilder
{

    private $grammar;

    private $first;

    private $follow;

    private $table;

    public function __construct(GrammarInterface $grammar)
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
            $this->checkConflicts();
            $table = new Table;
            $this->addProductionsFromNonTerminalMap($table);
            $this->table = $table;
        }
        return $this->table;
    }

    /**
     * @throws Exception
     */
    private function checkConflicts(): void
    {
        foreach ($this->grammar->getNonTerminalList() as $nonTerminalId) {
            $follow = $this->getFollow()->getTokens($nonTerminalId);
            $productionList = $this->grammar->getProductionList($nonTerminalId);
            foreach ($productionList as $i => $productionAlpha) {
                $firstAlpha = $this->getFirst()->getProductionTokens(...$productionAlpha);
                foreach ($productionList as $j => $productionBeta) {
                    if ($i == $j) {
                        continue;
                    }
                    $firstBeta = $this->getFirst()->getProductionTokens(...$productionBeta);
                    $firstFirst = array_intersect($firstAlpha, $firstBeta);
                    if (!empty($firstFirst)) {
                        $intersectionText = implode(', ', $firstFirst);
                        throw new Exception(
                            "Symbol {$nonTerminalId} has FIRST({$i})/FIRST({$j}) conflict: {$intersectionText}"
                        );
                    }
                    if ($this->getFirst()->productionHasEpsilon(...$productionBeta)) {
                        $firstFollow = array_intersect($follow, $firstAlpha);
                        if (!empty($firstFollow)) {
                            $intersectionText = implode(', ', $firstFollow);
                            throw new Exception(
                                "Symbol {$nonTerminalId} has FIRST({$i})/FOLLOW conflict (ε ∈ {$j}): {$intersectionText}"
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * @return FirstInterface
     */
    private function getFirst(): FirstInterface
    {
        if (!isset($this->first)) {
            $builder = new FirstBuilder($this->grammar);
            $this->first = $builder->getFirst();
        }
        return $this->first;
    }

    /**
     * @return FollowInterface
     */
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
        foreach ($this->grammar->getNonTerminalList() as $symbolId) {
            foreach ($this->grammar->getProductionList($symbolId) as $productionIndex => $production) {
                $this->addProductionFirsts($table, $symbolId, $productionIndex, ...$production);
                $this->addProductionFollows($table, $symbolId, $productionIndex, ...$production);
            }
        }
    }

    /**
     * @param Table $table
     * @param int $symbolId
     * @param int $productionIndex
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    private function addProductionFirsts(Table $table, int $symbolId, int $productionIndex, int ...$symbolIdList): void
    {
        $productionFirsts = $this->getFirst()->getProductionTokens(...$symbolIdList);
        foreach ($productionFirsts as $tokenId) {
            $table->addProduction($symbolId, $tokenId, $productionIndex);
        }
    }

    /**
     * @param Table $table
     * @param int $symbolId
     * @param int $productionIndex
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    private function addProductionFollows(Table $table, int $symbolId, int $productionIndex, int ...$symbolIdList): void
    {
        if (!$this->getFirst()->productionHasEpsilon(...$symbolIdList)) {
            return;
        }
        $productionFollows = $this->getFollow()->getTokens($symbolId);
        foreach ($productionFollows as $tokenId) {
            $table->addProduction($symbolId, $tokenId, $productionIndex);
        }
    }
}
