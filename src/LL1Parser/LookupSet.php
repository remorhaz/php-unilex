<?php

namespace Remorhaz\UniLex\LL1Parser;

abstract class LookupSet
{

    private $tokenMap = [];

    /**
     * This counter increases each time changes are made to set.
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
            $this->increaseChangeCount(count($tokenIdList));
            return;
        }
        $newTokenIdList = array_diff($tokenIdList, $this->tokenMap[$nonTerminalId]);
        if (!empty($newTokenIdList)) {
            $this->tokenMap[$nonTerminalId] = array_merge($this->tokenMap[$nonTerminalId], $newTokenIdList);
            $this->increaseChangeCount(count($newTokenIdList));
        }
    }

    protected function increaseChangeCount(int $amount = 1): void
    {
        $this->changeCount += $amount;
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

    protected function getOne(int $nonTerminalId): array
    {
        return $this->tokenMap[$nonTerminalId] ?? [];
    }
}
