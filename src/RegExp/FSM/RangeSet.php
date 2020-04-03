<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class RangeSet
{

    /**
     * @var Range[]
     */
    private $rangeList = [];

    public static function loadUnsafe(Range ...$rangeList): self
    {
        $rangeSet = new self();
        $rangeSet->rangeList = $rangeList;

        return $rangeSet;
    }

    /**
     * RangeSet constructor.
     *
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
        $rangeList = $this->getSortedRangeList(...$rangeList);
        $existingIndex = 0;
        $addedIndex = 0;
        $newRangeList = [];
        /** @var Range $rangeBuffer */
        $rangeBuffer = null;
        while (true) {
            $existingRange = $this->rangeList[$existingIndex] ?? null;
            $addedRange = $rangeList[$addedIndex] ?? null;

            if (isset($existingRange)) {
                if (isset($addedRange) && $existingRange->getStart() > $addedRange->getStart()) {
                    $pickedRange = $addedRange;
                    $addedIndex++;
                } else {
                    $pickedRange = $existingRange;
                    $existingIndex++;
                }
            } elseif (isset($addedRange)) {
                $pickedRange = $addedRange;
                $addedIndex++;
            } else {
                if (isset($rangeBuffer)) {
                    $newRangeList[] = $rangeBuffer;
                }
                break;
            }

            if (isset($rangeBuffer)) {
                if ($rangeBuffer->containsFinishOf($pickedRange)) {
                    continue;
                }
                if ($rangeBuffer->containsStartOf($pickedRange) || $pickedRange->follows($rangeBuffer)) {
                    $rangeBuffer = $pickedRange->copyAfterStartOf($rangeBuffer);
                    continue;
                }
                $newRangeList[] = $rangeBuffer;
            }
            $rangeBuffer = $pickedRange;
        }

        $this->rangeList = $newRangeList;
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

    private function getSortedRangeList(Range ...$rangeList): array
    {
        if (isset($rangeList[1])) {
            $sortByFrom = function (Range $rangeOne, Range $rangeTwo): int {
                return $rangeOne->getStart() <=> $rangeTwo->getStart();
            };
            usort($rangeList, $sortByFrom);
        }

        return $rangeList;
    }
}
