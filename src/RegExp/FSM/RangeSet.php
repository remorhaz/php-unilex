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
     * @return array
     * @throws Exception
     */
    public function getRangesDiff(array ...$rangeList): array
    {
        throw new Exception("Calculating ranges diff is not implemented yet");
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
            $fromInExistingRange = $from >= $existingFrom && $from <= $existingTo;
            $toInExistingRange = $to >= $existingFrom && $to <= $existingTo;
            $nextToExistingRange = $from == $existingTo + 1;
            $prevToExistingRange = $to + 1 == $existingFrom;
            $rangesIntersectOrTouch =
                $fromInExistingRange || $toInExistingRange || $nextToExistingRange || $prevToExistingRange;
            if (!$rangesIntersectOrTouch) {
                $newRangeList[] = $existingRange;
                continue;
            }
            if ($fromInExistingRange || $nextToExistingRange) {
                $from = $existingFrom;
            }
            if ($toInExistingRange || $prevToExistingRange) {
                $to = $existingFrom;
            }
        }
        $newRangeList[] = [$from, $to];
        $sortByFrom = function (array $rangeOne, array $rangeTwo): int {
            return $rangeOne[0] <=> $rangeTwo[0];
        };
        usort($newRangeList, $sortByFrom);
        $this->rangeList = $newRangeList;
    }
}
