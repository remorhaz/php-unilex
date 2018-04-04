<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class Range
{

    private $start;

    private $finish;

    /**
     * @param int $start
     * @param int|null $finish
     * @throws Exception
     */
    public function __construct(int $start, int $finish = null)
    {
        if (isset($finish) && $start > $finish) {
            throw new Exception("Invalid range {$start}..{$finish}");
        }
        $this->start = $start;
        $this->finish = $finish ?? $start;
    }

    public function __toString(): string
    {
        return $this->start == $this->finish ? "{$this->start}" : "{$this->start}..{$this->finish}";
    }

    /**
     * @param array ...$rangeDataList
     * @return self[]
     * @throws Exception
     */
    public static function importList(array ...$rangeDataList): array
    {
        $rangeList = [];
        foreach ($rangeDataList as $rangeData) {
            $rangeList[] = new self(...$rangeData);
        }
        return $rangeList;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getFinish(): int
    {
        return $this->finish;
    }

    public function containsChar(int $char): bool
    {
        return $this->getStart() <= $char && $char <= $this->getFinish();
    }

    public function startsBeforeStartOf(Range $range): bool
    {
        return $this->getStart() < $range->getStart();
    }

    public function endsBeforeStartOf(Range $range): bool
    {
        return $this->getFinish() < $range->getStart();
    }

    public function endsBeforeFinishOf(Range $range): bool
    {
        return $this->getFinish() < $range->getFinish();
    }

    public function intersects(Range $range): bool
    {
        return !$this->endsBeforeStartOf($range) && !$range->endsBeforeStartOf($this);
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyBeforeStartOf(Range $range): self
    {
        return new self($this->getStart(), $range->getStart() - 1);
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyAfterFinishOf(Range $range): self
    {
        return new self($range->getFinish() + 1, $this->getFinish());
    }

    public function containsStartOf(Range $range): bool
    {
        return $this->getStart() <= $range->getStart() && $range->getStart() <= $this->getFinish();
    }

    public function containsFinishOf(Range $range): bool
    {
        return $this->getStart() <= $range->getFinish() && $range->getFinish() <= $this->getFinish();
    }

    public function follows(Range $range): bool
    {
        return $this->getStart() == $range->getFinish() + 1;
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyAfterStartOf(Range $range): self
    {
        return new self($range->getStart(), $this->getFinish());
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyBeforeFinishOf(Range $range): self
    {
        return new self($this->getStart(), $range->getFinish());
    }

    public function export(): array
    {
        return [$this->getStart(), $this->getFinish()];
    }
}
