<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

/**
 * Helper to calculate FIRST() sets. It's a part of LL(1) lookup table generation algorithm. FIRST(X) set
 * contains terminals (and, optionally, ε-production) that can occur as a starting token in production X.
 */
class First extends Set implements FirstInterface
{
    /**
     * @var array<int, bool>
     */
    private array $epsilonMap = [];

    /**
     * Returns FIRST(X) set.
     *
     * @param int ...$symbolIdList
     * @return list<int>
     */
    public function getProductionTokens(int ...$symbolIdList): array
    {
        $first = [];
        foreach ($symbolIdList as $symbolId) {
            $first = array_merge($first, $this->getTokens($symbolId));
            if (!$this->productionHasEpsilon($symbolId)) {
                break;
            }
        }

        return $first;
    }

    /**
     * Adds ε-production to FIRST(X) set.
     *
     * @param int $symbolId
     */
    public function addEpsilon(int $symbolId): void
    {
        if ($this->hasEpsilon($symbolId)) {
            return;
        }

        $this->epsilonMap[$symbolId] = true;
        $this->increaseChangeCount();
    }

    /**
     * Reports presence of ε-production in FIRST(X) sets for all given X.
     *
     * @param int ...$symbolIdList
     * @return bool
     */
    public function productionHasEpsilon(int ...$symbolIdList): bool
    {
        if (empty($symbolIdList)) {
            return true;
        }

        foreach ($symbolIdList as $symbolId) {
            if (!$this->hasEpsilon($symbolId)) {
                return false;
            }
        }

        return true;
    }

    public function hasEpsilon(int $symbolId): bool
    {
        return $this->epsilonMap[$symbolId] ?? false;
    }

    /**
     * Adds all tokens from source production's FIRST(X) to target production's FIRST(Y).
     */
    public function mergeProductionTokens(int $targetSymbolId, int ...$sourceSymbolIdList): void
    {
        $this->addToken($targetSymbolId, ...$this->getProductionTokens(...$sourceSymbolIdList));
    }

    public function mergeProductionEpsilons(int $targetSymbolId, int ...$sourceSymbolIdList): void
    {
        if ($this->productionHasEpsilon(...$sourceSymbolIdList)) {
            $this->addEpsilon($targetSymbolId);
        }
    }
}
