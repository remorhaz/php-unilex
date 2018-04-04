<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class RangeSet
{

    /**
     * @var Range[]
     */
    private $rangeList = [];

    /**
     * RangeSet constructor.
     * @param Range ...$rangeList
     * @throws Exception
     */
    public function __construct(Range ...$rangeList)
    {
        if (!empty($rangeList)) {
            $this->addRange(...$rangeList);
        }
    }

    /**
     * @param array ...$rangeDataList
     * @return RangeSet
     * @throws Exception
     */
    public static function import(array ...$rangeDataList): self
    {
        return new self(...Range::importList(...$rangeDataList));
    }

    public function export(): array
    {
        $rangeDataList = [];
        foreach ($this->getRanges() as $range) {
            $rangeDataList[] = $range->export();
        }
        return $rangeDataList;
    }

    /**
     * @param Range ...$rangeList
     * @throws Exception
     */
    public function addRange(Range ...$rangeList): void
    {
        foreach ($rangeList as $range) {
            $this->mergeSingleRange($range);
        }
    }

    /**
     * @return Range[]
     */
    public function getRanges(): array
    {
        return $this->rangeList;
    }

    public function isEmpty(): bool
    {
        return empty($this->rangeList);
    }

    /**
     * @param Range $range
     * @throws Exception
     */
    private function mergeSingleRange(Range $range): void
    {
        if (empty($this->rangeList)) {
            $this->rangeList = [$range];
            return;
        }
        $newRangeList = [];
        foreach ($this->rangeList as $existingRange) {
            if ($existingRange->containsStartOf($range) || $range->follows($existingRange)) {
                $range->alignStart($existingRange);
                continue;
            }
            if ($existingRange->containsFinishOf($range) || $existingRange->follows($range)) {
                $range->alignFinish($existingRange);
                continue;
            }
            $newRangeList[] = $existingRange;
        }
        $newRangeList[] = $range;
        $this->setSortedRangeList(...$newRangeList);
    }

    private function setSortedRangeList(Range ...$rangeList): void
    {
        $sortByFrom = function (Range $rangeOne, Range $rangeTwo): int {
            return $rangeOne->getStart() <=> $rangeTwo->getStart();
        };
        usort($rangeList, $sortByFrom);
        $this->rangeList = $rangeList;
    }
}
