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
     * @param Range[] ...$rangeList
     * @throws Exception
     */
    public function __construct(Range ...$rangeList)
    {
        if (!empty($rangeList)) {
            $this->addRange(...$rangeList);
        }
    }

    /**
     * @param Range[] ...$rangeList
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

    /**
     * @param Range[] ...$rangeList
     * @return self
     * @throws Exception
     */
    public function getDiff(Range ...$rangeList): self
    {
        $diffRangeSet = clone $this;
        foreach ($rangeList as $range) {
            $diffRangeSet->diffSingleRange($range);
        }
        return $diffRangeSet;
    }

    /**
     * @param Range $range
     * @throws Exception
     */
    private function diffSingleRange(Range $range): void
    {
        if (empty($this->rangeList)) {
            $this->rangeList = [$range];
            return;
        }
        $rangeSet = new self;
        $isRangeProcessed = false;
        foreach ($this->rangeList as $existingRange) {
            if ($range->getFrom() < $existingRange->getFrom()) {
                // Range start before existing range starts
                if ($range->getTo() < $existingRange->getFrom()) {
                    // Entire range is to the left from existing one - copy both as is
                    if (!$isRangeProcessed) {
                        $rangeSet->addRange($range);
                        $isRangeProcessed = true;
                    }
                    $rangeSet->addRange($existingRange);
                    continue;
                }
                // copy part of range to the left from existing one
                $rangeSet->addRange(new Range($range->getFrom(), $existingRange->getFrom() - 1));
                $range->setFrom($existingRange->getFrom());
            } elseif ($existingRange->getFrom() < $range->getFrom()) {
                // Range starts after existing range starts
                if ($existingRange->getTo() < $range->getFrom()) {
                    // Entire range is to the right from existing one - copy existing range as is
                    $rangeSet->addRange($existingRange);
                    continue;
                }
                // copy part of existing range to the left from range
                $rangeSet->addRange(new Range($existingRange->getFrom(), $range->getFrom() - 1));
            }
            if ($existingRange->getTo() < $range->getTo()) {
                $range->setFrom($existingRange->getTo() + 1);
                continue;
            }
            if ($range->getTo() < $existingRange->getTo()) {
                // Range ends before existing one ends - copy right part of existing range
                $rangeSet->addRange(new Range($range->getTo() + 1, $existingRange->getTo()));
            }
            $isRangeProcessed = true;
        }
        if (!$isRangeProcessed) {
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
        if ($range->getFrom() > $range->getTo()) {
            throw new Exception("Invalid range {$range}");
        }
        if (empty($this->rangeList)) {
            $this->rangeList = [$range];
            return;
        }
        $newRangeList = [];
        foreach ($this->rangeList as $existingRange) {
            $extendLeft =
                $existingRange->getFrom() <= $range->getFrom() && $range->getFrom() <= $existingRange->getTo() ||
                $range->getFrom() == $existingRange->getTo() + 1;
            $extendRight =
                $existingRange->getFrom() <= $range->getTo() && $range->getTo() <= $existingRange->getTo() ||
                $range->getTo() + 1 == $existingRange->getFrom();
            if (!$extendLeft && !$extendRight) {
                $newRangeList[] = $existingRange;
                continue;
            }
            if ($extendLeft) {
                $range->setFrom($existingRange->getFrom());
            }
            if ($extendRight) {
                $range->setTo($existingRange->getTo());
            }
        }
        $newRangeList[] = $range;
        $this->setSortedRangeList(...$newRangeList);
    }

    private function setSortedRangeList(Range ...$rangeList): void
    {
        $sortByFrom = function (Range $rangeOne, Range $rangeTwo): int {
            return $rangeOne->getFrom() <=> $rangeTwo->getFrom();
        };
        usort($rangeList, $sortByFrom);
        $this->rangeList = $rangeList;
    }
}
