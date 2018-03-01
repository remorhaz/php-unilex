<?php

namespace Remorhaz\UniLex\LL1Parser;

/**
 * Helper to calculate FIRST() sets. It's a part of LL(1) lookup table generation algorithm. FIRST(X) set
 * contains terminals (and, optionally, ε-production) that can occur as a starting token in production X.
 */
class LookupFirst implements LookupFirstInfoInterface
{

    private $tokenMap = [];

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
     * @param int $nonTerminalId
     * @param int[] ...$tokenIdList
     */
    public function addToken(int $nonTerminalId, int ...$tokenIdList): void
    {
        if (empty($tokenIdList)) {
            return;
        }
        if (!isset($this->tokenMap[$nonTerminalId])) {
            $this->tokenMap[$nonTerminalId] = $tokenIdList;
            $this->changeCount += count($tokenIdList);
            return;
        }
        $newTokenIdList = array_diff($tokenIdList, $this->tokenMap[$nonTerminalId]);
        if (!empty($newTokenIdList)) {
            $this->tokenMap[$nonTerminalId] = array_merge($this->tokenMap[$nonTerminalId], $newTokenIdList);
            $this->changeCount += count($newTokenIdList);
        }
    }

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
            $first = array_merge($first, $this->tokenMap[$nonTerminalId] ?? []);
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
        $this->changeCount++;
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
