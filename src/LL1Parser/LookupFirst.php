<?php

namespace Remorhaz\UniLex\LL1Parser;

/**
 * Helper to calculate FIRST() sets. It's a part of LL(1) lookup table generation algorithm. FIRST(X) set
 * contains terminals (and, optionally, ε-production) that can occur as a starting token in production X.
 */
class LookupFirst
{

    /**
     * FIRST(X) sets map. Key is production ID, value is list of token IDs.
     *
     * @var array[]
     */
    private $tokenMap = [];

    /**
     * FIRST(X) ε-production presence marker. Key is production ID, value is true.
     *
     * @var true[]
     */
    private $epsilonMap = [];

    /**
     * This counter increases each time a single token or ε-production is added to FIRST(X).
     *
     * @var int
     */
    private $changeCount = 0;

    /**
     * Adds list of productions to FIRST(X) set.
     *
     * @param int $productionId
     * @param int[] ...$tokenIdList
     */
    public function add(int $productionId, int ...$tokenIdList): void
    {
        if (empty($tokenIdList)) {
            return;
        }
        if (!isset($this->tokenMap[$productionId])) {
            $this->tokenMap[$productionId] = $tokenIdList;
            $this->changeCount += count($tokenIdList);
            return;
        }
        $newTokenIdList = array_diff($tokenIdList, $this->tokenMap[$productionId]);
        if (!empty($newTokenIdList)) {
            $this->tokenMap[$productionId] = array_merge($this->tokenMap[$productionId], $newTokenIdList);
            $this->changeCount += count($newTokenIdList);
        }
    }

    /**
     * Returns FIRST(X) set.
     *
     * @param int $productionId
     * @return array
     */
    public function get(int $productionId): array
    {
        return $this->tokenMap[$productionId] ?? [];
    }

    /**
     * Adds ε-production to FIRST(X) set.
     *
     * @param int $productionId
     */
    public function addEpsilon(int $productionId): void
    {
        if ($this->hasEpsilon($productionId)) {
            return;
        }
        $this->epsilonMap[$productionId] = true;
        $this->changeCount++;
    }

    /**
     * Reports presence of ε-production in FIRST(X) set.
     *
     * @param int[] $productionIdList
     * @return bool
     */
    public function hasEpsilon(int ...$productionIdList): bool
    {
        foreach ($productionIdList as $productionId) {
            if (!($this->epsilonMap[$productionId] ?? false)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns amount of changes in all FIRST(X) sets since last reset.
     *
     * @return int
     */
    public function getChangeCount(): int
    {
        return $this->changeCount;
    }

    /**
     * Resets FIRST(X) changes counter.
     */
    public function resetChangeCount(): void
    {
        $this->changeCount = 0;
    }

    /**
     * Adds all tokens from source production's FIRST(X) to target production's FIRST(Y).
     *
     * @param int $sourceProductionId
     * @param int $targetProductionId
     */
    public function merge(int $sourceProductionId, int $targetProductionId): void
    {
        if ($sourceProductionId == $targetProductionId) {
            return;
        }
        $this->add($targetProductionId, ...$this->get($sourceProductionId));
    }
}
