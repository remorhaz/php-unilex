<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

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
     * @throws Exception
     */
    public function and(RangeSet $rangeSet, RangeSet $anotherRangeSet): RangeSet
    {
        $result = new RangeSet();
        foreach ($anotherRangeSet->getRanges() as $range) {
            $andRangeSetPart = $this->andSingleRange($rangeSet, $range);
            $result->addRange(...$andRangeSetPart->getRanges());
        }

        return $result;
    }

    /**
     * @param RangeSet $rangeSetPart
     * @param Range    $range
     * @return RangeSet
     * @throws Exception
     */
    private function andSingleRange(RangeSet $rangeSetPart, Range $range): RangeSet
    {
        $rangeSet = new RangeSet();
        if ($rangeSetPart->isEmpty()) {
            return $rangeSet;
        }
        foreach ($rangeSetPart->getRanges() as $existingRange) {
            if (!$existingRange->intersects($range)) {
                continue;
            }
            if ($range->startsBeforeStartOf($existingRange)) {
                $range = $range->copyAfterStartOf($existingRange);
            }
            if ($existingRange->endsBeforeFinishOf($range)) {
                $rangeSet->addRange($range->copyBeforeFinishOf($existingRange));
                $range = $range->copyAfterFinishOf($existingRange);
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
     * @throws Exception
     */
    public function xor(RangeSet $rangeSet, RangeSet $anotherRangeSet): RangeSet
    {
        $ranges = $rangeSet->getRanges();
        $otherRanges = $anotherRangeSet->getRanges();
        $index = 0;
        $anotherIndex = 0;
        $newRangeList = [];
        /** @var Range $rangeBuffer */
        $rangeBuffer = null;
        while (true) {
            $range = $ranges[$index] ?? null;
            $anotherRange = $otherRanges[$anotherIndex] ?? null;

            if (isset($range)) {
                if (isset($anotherRange) && $range->getStart() > $anotherRange->getStart()) {
                    $pickedRange = $anotherRange;
                    $anotherIndex++;
                } else {
                    $pickedRange = $range;
                    $index++;
                }
            } elseif (isset($anotherRange)) {
                $pickedRange = $anotherRange;
                $anotherIndex++;
            } else {
                if (isset($rangeBuffer)) {
                    $newRangeList[] = $rangeBuffer;
                }
                break;
            }

            if (isset($rangeBuffer)) {
                if ($rangeBuffer->intersects($pickedRange)) {
                    if ($rangeBuffer->startsBeforeStartOf($pickedRange)) {
                        $newRangeList[] = $rangeBuffer->copyBeforeStartOf($pickedRange);
                        $rangeBuffer = $rangeBuffer->copyAfterStartOf($pickedRange);
                    } elseif ($pickedRange->startsBeforeStartOf($rangeBuffer)) {
                        $newRangeList[] = $pickedRange->copyBeforeStartOf($rangeBuffer);
                        $rangeBuffer = $pickedRange->copyAfterStartOf($rangeBuffer);
                    }
                    if ($rangeBuffer->endsBeforeFinishOf($pickedRange)) {
                        $rangeBuffer = $pickedRange->copyAfterFinishOf($rangeBuffer);
                    } elseif ($pickedRange->endsBeforeFinishOf($rangeBuffer)) {
                        $rangeBuffer = $rangeBuffer->copyAfterFinishOf($pickedRange);
                    } else {
                        $rangeBuffer = null;
                    }
                    continue;
                }
                if ($pickedRange->follows($rangeBuffer)) {
                    $rangeBuffer = $rangeBuffer->copyBeforeFinishOf($pickedRange);
                    continue;
                }
                $newRangeList[] = $rangeBuffer;
            }
            $rangeBuffer = $pickedRange;
        }

        return RangeSet::loadUnsafe(...$newRangeList);
    }
}
