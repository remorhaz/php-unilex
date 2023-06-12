<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

abstract class Set
{
    /**
     * @var array<int, list<int>>
     */
    private array $tokenMap = [];

    /**
     * This counter increases each time changes are made to set.
     */
    private int $changeCount = 0;

    /**
     * Adds list of tokens to the set.
     */
    public function addToken(int $symbolId, int ...$tokenIdList): void
    {
        if (empty($tokenIdList)) {
            return;
        }

        if (!isset($this->tokenMap[$symbolId])) {
            $this->tokenMap[$symbolId] = $tokenIdList;
            $this->increaseChangeCount(count($tokenIdList));

            return;
        }

        $newTokenIdList = array_diff($tokenIdList, $this->tokenMap[$symbolId]);
        if (empty($newTokenIdList)) {
            return;
        }

        $this->tokenMap[$symbolId] = array_merge($this->tokenMap[$symbolId], $newTokenIdList);
        $this->increaseChangeCount(count($newTokenIdList));
    }

    protected function increaseChangeCount(int $amount = 1): void
    {
        $this->changeCount += $amount;
    }

    /**
     * @param int $symbolId
     * @return list<int>
     */
    public function getTokens(int $symbolId): array
    {
        return $this->tokenMap[$symbolId] ?? [];
    }

    /**
     * Returns amount of changes since last reset.
     */
    public function getChangeCount(): int
    {
        return $this->changeCount;
    }

    /**
     * Resets changes counter.
     */
    public function resetChangeCount(): void
    {
        $this->changeCount = 0;
    }

    /**
     * Merges token sets.
     */
    public function mergeTokens(int $targetSymbolId, int $sourceSymbolId): void
    {
        $this->addToken($targetSymbolId, ...$this->getTokens($sourceSymbolId));
    }
}
