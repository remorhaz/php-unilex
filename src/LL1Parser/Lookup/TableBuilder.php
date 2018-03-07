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
            $this->checkGrammarConflicts();
            $table = new Table;
            $this->addProductionsFromNonTerminalMap($table);
            $this->table = $table;
        }
        return $this->table;
    }

    /**
     * @throws Exception
     */
    private function checkGrammarConflicts(): void
    {
        foreach ($this->grammar->getNonTerminalList() as $symbolId) {
            $productionList = $this->grammar->getProductionList($symbolId);
            foreach ($productionList as $iAlpha => $alpha) {
                foreach ($productionList as $iBeta => $beta) {
                    if ($iAlpha == $iBeta) {
                        continue;
                    }
                    $this->checkFirstFirstConflict($symbolId, $iAlpha, $alpha, $iBeta, $beta);
                    $this->checkFirstFollowConflict($symbolId, $iAlpha, $alpha, $iBeta, $beta);
                }
            }
        }
    }

    /**
     * @param int $symbolId
     * @param int $iAlpha
     * @param array $alpha
     * @param int $iBeta
     * @param array $beta
     * @throws Exception
     */
    private function checkFirstFirstConflict(int $symbolId, int $iAlpha, array $alpha, int $iBeta, array $beta): void
    {
        $firstAlpha = $this->getFirst()->getProductionTokens(...$alpha);
        $firstBeta = $this->getFirst()->getProductionTokens(...$beta);
        $message = "Symbol {$symbolId} has FIRST({$iAlpha})/FIRST({$iBeta}) conflict";
        $this->checkConflict($firstAlpha, $firstBeta, $message);
    }

    /**
     * @param int $symbolId
     * @param int $iAlpha
     * @param array $alpha
     * @param int $iBeta
     * @param array $beta
     * @throws Exception
     */
    private function checkFirstFollowConflict(int $symbolId, int $iAlpha, array $alpha, int $iBeta, array $beta): void
    {
        if (!$this->getFirst()->productionHasEpsilon(...$beta)) {
            return;
        }
        $follow = $this->getFollow()->getTokens($symbolId);
        $firstAlpha = $this->getFirst()->getProductionTokens(...$alpha);
        $message = "Symbol {$symbolId} has FIRST({$iAlpha})/FOLLOW conflict (ε ∈ {$iBeta})";
        $this->checkConflict($follow, $firstAlpha, $message);
    }

    /**
     * @param array $tokenListA
     * @param array $tokenListB
     * @param string $message
     * @throws Exception
     */
    private function checkConflict(array $tokenListA, array $tokenListB, string $message): void
    {
        $conflict = array_intersect($tokenListA, $tokenListB);
        if (!empty($conflict)) {
            $conflictText = implode(', ', $conflict);
            throw new Exception("{$message}: {$conflictText}");
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
        foreach ($this->grammar->getFullProductionList() as [$symbolId, $productionIndex, $production]) {
            $this->addProductionFirsts($table, $symbolId, $productionIndex, ...$production);
            $this->addProductionFollows($table, $symbolId, $productionIndex, ...$production);
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
