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
     * @param Range ...$rangeList
     * @return self
     * @throws Exception
     */
    public function getDiff(Range ...$rangeList): self
    {
        $diffRangeSet = clone $this;
        foreach ($rangeList as $range) {
            $diffRangeSet->diffSingleRange(clone $range);
        }
        return $diffRangeSet;
    }

    /**
     * @param Range ...$rangeList
     * @return RangeSet
     * @throws Exception
     */
    public function getAnd(Range ...$rangeList): self
    {
        $andRangeSet = new self;
        foreach ($rangeList as $range) {
            $andRangeSetPart = clone $this;
            $andRangeSetPart->andSingleRange(clone $range);
            $andRangeSet->addRange(...$andRangeSetPart->getRanges());
        }
        return $andRangeSet;
    }

    /**
     * @param Range ...$rangeList
     * @return bool
     * @throws Exception
     */
    public function isSame(Range ...$rangeList): bool
    {
        return $this->getDiff(...$rangeList)->isEmpty();
    }

    /**
     * @param Range $range
     * @throws Exception
     * @todo Rename to XOR
     */
    private function diffSingleRange(Range $range): void
    {
        if (empty($this->rangeList)) {
            $this->rangeList = [$range];
            return;
        }
        $rangeSet = new self;
        $shouldAddRange = true;
        foreach ($this->rangeList as $existingRange) {
            if (!$existingRange->intersects($range)) {
                $rangeSet->addRange($existingRange);
                continue;
            }
            if ($range->startsBeforeStartOf($existingRange)) {
                $rangeSet->addRange($range->sliceBeforeStartOf($existingRange));
            } elseif ($existingRange->startsBeforeStartOf($range)) {
                $rangeSet->addRange($existingRange->copyBeforeStartOf($range));
            }
            if ($existingRange->endsBeforeFinishOf($range)) {
                $range->sliceBeforeFinishOf($existingRange);
                continue;
            } elseif ($range->endsBeforeFinishOf($existingRange)) {
                $rangeSet->addRange($existingRange->copyAfterFinishOf($range));
            }
            $shouldAddRange = false;
        }
        if ($shouldAddRange) {
            $rangeSet->addRange($range);
        }
        $this->rangeList = $rangeSet->getRanges();
    }

    /**
     * @param Range $range
     * @throws Exception
     */
    private function andSingleRange(Range $range): void
    {
        if (empty($this->rangeList)) {
            return;
        }
        $rangeSet = new self;
        foreach ($this->rangeList as $existingRange) {
            if (!$existingRange->intersects($range)) {
                continue;
            }
            if ($range->startsBeforeStartOf($existingRange)) {
                $range->sliceBeforeStartOf($existingRange);
            }
            if ($existingRange->endsBeforeFinishOf($range)) {
                $rangeSet->addRange($range->sliceBeforeFinishOf($existingRange));
                continue;
            }
            $rangeSet->addRange($range);
        }
        $this->rangeList = $rangeSet->getRanges();
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
