<?php

namespace Remorhaz\UniLex\LL1Parser;

/**
 * Helper to calculate FIRST() sets. It's a part of LL(1) lookup table generation algorithm. FIRST(X) set
 * contains terminals (and, optionally, ε-production) that can occur as a starting token in production X.
 */
class LookupFirst extends LookupSet implements LookupFirstInfoInterface
{

    private $epsilonMap = [];

    /**
     * Returns FIRST(X) set.
     *
     * @param int[] ...$nonTerminalIdList
     * @return array
     */
    public function get(int ...$nonTerminalIdList): array
    {
        $first = [];
        foreach ($nonTerminalIdList as $nonTerminalId) {
            $first = array_merge($first, $this->getOne($nonTerminalId));
            if (!$this->hasEpsilon($nonTerminalId)) {
                break;
            }
        }
        return $first;
    }

    /**
     * Adds ε-production to FIRST(X) set.
     *
     * @param int $nonTerminalId
     */
    public function addEpsilon(int $nonTerminalId): void
    {
        if ($this->hasEpsilon($nonTerminalId)) {
            return;
        }
        $this->epsilonMap[$nonTerminalId] = true;
        $this->increaseChangeCount();
    }

    /**
     * Reports presence of ε-production in FIRST(X) sets for all given X.
     *
     * @param int[] ...$nonTerminalIdList
     * @return bool
     */
    public function hasEpsilon(int ...$nonTerminalIdList): bool
    {
        if (empty($nonTerminalIdList)) {
            return true;
        }
        foreach ($nonTerminalIdList as $productionId) {
            if (!($this->epsilonMap[$productionId] ?? false)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds all tokens from source production's FIRST(X) to target production's FIRST(Y).
     *
     * @param int $targetNonTerminalId
     * @param int[] ...$sourceNonTerminalIdList
     */
    public function mergeTokens(int $targetNonTerminalId, int ...$sourceNonTerminalIdList): void
    {
        $this->addToken($targetNonTerminalId, ...$this->get(...$sourceNonTerminalIdList));
    }

    public function mergeEpsilons(int $targetNonTerminalId, int ...$sourceNonTerminalIdList): void
    {
        if ($this->hasEpsilon(...$sourceNonTerminalIdList)) {
            $this->addEpsilon($targetNonTerminalId);
        }
    }
}
