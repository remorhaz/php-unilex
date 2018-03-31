<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class RangeSet
{

    private $rangeList = [];

    /**
     * RangeSet constructor.
     * @param array[] ...$rangeList
     * @throws Exception
     */
    public function __construct(array ...$rangeList)
    {
        if (!empty($rangeList)) {
            $this->addRange(...$rangeList);
        }
    }

    /**
     * @param array[] ...$rangeList
     * @throws Exception
     */
    public function addRange(array ...$rangeList): void
    {
        foreach ($rangeList as $range) {
            $this->mergeSingleRange(...$range);
        }
    }

    public function getRanges(): array
    {
        return $this->rangeList;
    }

    /**
     * @param array[] ...$rangeList
     * @return self
     * @throws Exception
     */
    public function getDiff(array ...$rangeList): self
    {
        $diffRangeSet = clone $this;
        foreach ($rangeList as $range) {
            $diffRangeSet->diffSingleRange(...$range);
        }
        return $diffRangeSet;
    }

    /**
     * @param int $from
     * @param int $to
     * @throws Exception
     */
    private function diffSingleRange(int $from, int $to): void
    {
        if ($from > $to) {
            throw new Exception("Invalid range {$from}..{$to}");
        }
        if (empty($this->rangeList)) {
            $this->rangeList = [[$from, $to]];
            return;
        }
        $rangeSet = new self;
        $isRangeProcessed = false;
        foreach ($this->rangeList as $existingRange) {
            [$existingFrom, $existingTo] = $existingRange;
            if ($from < $existingFrom) {
                // Range start before existing range starts
                if ($to < $existingFrom) {
                    // Entire range is to the left from existing one - copy both as is
                    if (!$isRangeProcessed) {
                        $rangeSet->addRange([$from, $to]);
                        $isRangeProcessed = true;
                    }
                    $rangeSet->addRange([$existingFrom, $existingTo]);
                    continue;
                }
                $rangeSet->addRange([$from, $existingFrom - 1]); // copy part of range to the left from existing one
                $from = $existingFrom;
            } elseif ($existingFrom < $from) {
                // Range starts after existing range starts
                if ($existingTo < $from) {
                    // Entire range is to the right from existing one - copy existing range as is
                    $rangeSet->addRange([$existingFrom, $existingTo]);
                    continue;
                }
                $rangeSet->addRange([$existingFrom, $from - 1]); // copy part of existing range to the left from range
            }
            if ($existingTo < $to) {
                $from = $existingTo + 1;
                continue;
            }
            if ($to < $existingTo) {
                // Range ends before existing one ends - copy right part of existing range
                $rangeSet->addRange([$to + 1, $existingTo]);
            }
            $isRangeProcessed = true;
        }
        if (!$isRangeProcessed) {
            $rangeSet->addRange([$from, $to]);
        }
        $this->rangeList = $rangeSet->getRanges();
    }

    /**
     * @param int $from
     * @param int $to
     * @throws Exception
     */
    private function mergeSingleRange(int $from, int $to): void
    {
        if ($from > $to) {
            throw new Exception("Invalid range {$from}..{$to}");
        }
        if (empty($this->rangeList)) {
            $this->rangeList = [[$from, $to]];
            return;
        }
        $newRangeList = [];
        foreach ($this->rangeList as $existingRange) {
            [$existingFrom, $existingTo] = $existingRange;
            $extendLeft = $existingFrom <= $from && $from <= $existingTo || $from == $existingTo + 1;
            $extendRight = $existingFrom <= $to && $to <= $existingTo || $to + 1 == $existingFrom;
            if (!$extendLeft && !$extendRight) {
                $newRangeList[] = $existingRange;
                continue;
            }
            if ($extendLeft) {
                $from = $existingFrom;
            }
            if ($extendRight) {
                $to = $existingTo;
            }
        }
        $newRangeList[] = [$from, $to];
        $this->setSortedRangeList(...$newRangeList);
    }

    private function setSortedRangeList(array ...$rangeList): void
    {
        $sortByFrom = function (array $rangeOne, array $rangeTwo): int {
            return $rangeOne[0] <=> $rangeTwo[0];
        };
        usort($rangeList, $sortByFrom);
        $this->rangeList = $rangeList;
    }
}
