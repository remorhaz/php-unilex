<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class RangeSetCalc
{

    public function equals(RangeSet $rangeSet, RangeSet $anotherRangeSet): bool
    {
        $rangeList = $rangeSet->getRanges();
        $anotherRangeList = $anotherRangeSet->getRanges();
        if (count($rangeList) != count($anotherRangeList)) {
            return false;
        }
        foreach ($rangeList as $index => $range) {
            $anotherRange = $anotherRangeList[$index];
            if ($range->getStart() != $anotherRange->getStart()) {
                return false;
            }
            if ($range->getFinish() != $anotherRange->getFinish()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param RangeSet $rangeSet
     * @param RangeSet $anotherRangeSet
     * @return RangeSet
     * @throws \Remorhaz\UniLex\Exception
     */
    public function and(RangeSet $rangeSet, RangeSet $anotherRangeSet): RangeSet
    {
        $result = new RangeSet;
        foreach ($anotherRangeSet->getRanges() as $range) {
            $andRangeSetPart = $this->andSingleRange(clone $rangeSet, clone $range);
            $result->addRange(...$andRangeSetPart->getRanges());
        }

        return $result;
    }

    /**
     * @param RangeSet $rangeSetPart
     * @param Range $range
     * @return RangeSet
     * @throws \Remorhaz\UniLex\Exception
     */
    private function andSingleRange(RangeSet $rangeSetPart, Range $range): RangeSet
    {
        $rangeSet = new RangeSet;
        if ($rangeSetPart->isEmpty()) {
            return $rangeSet;
        }
        foreach ($rangeSetPart->getRanges() as $existingRange) {
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
        return $rangeSet;
    }

    /**
     * @param RangeSet $rangeSet
     * @param RangeSet $anotherRangeSet
     * @return RangeSet
     * @throws \Remorhaz\UniLex\Exception
     */
    public function xor(RangeSet $rangeSet, RangeSet $anotherRangeSet): RangeSet
    {
        $result = clone $rangeSet;
        foreach ($anotherRangeSet->getRanges() as $range) {
            $result = $this->xorSingleRange($result, clone $range);
        }
        return $result;
    }

    /**
     * @param RangeSet $rangeSetPart
     * @param Range $range
     * @return RangeSet
     * @throws \Remorhaz\UniLex\Exception
     */
    private function xorSingleRange(RangeSet $rangeSetPart, Range $range): RangeSet
    {
        $rangeSet = new RangeSet;
        if ($rangeSetPart->isEmpty()) {
            $rangeSet->addRange($range);
            return $rangeSet;
        }
        $shouldAddRange = true;
        foreach ($rangeSetPart->getRanges() as $existingRange) {
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
        return $rangeSet;
    }
}
