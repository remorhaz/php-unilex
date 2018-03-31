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
     * @param array[] ...$rangeDataList
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

    /**
     * @param int $start
     * @throws Exception
     */
    private function setStart(int $start): void
    {
        if ($start > $this->getFinish()) {
            throw new Exception("Invalid range {$start}..{$this->getFinish()}");
        }

        $this->start = $start;
    }

    /**
     * @param int $finish
     * @throws Exception
     */
    private function setFinish(int $finish): void
    {
        if ($this->getStart() > $finish) {
            throw new Exception("Invalid range {$this->getStart()}..{$finish}");
        }
        $this->finish = $finish;
    }

    public function containsChar(int $char): bool
    {
        return $this->getStart() <= $char && $char <= $this->getFinish();
    }

    public function startsBeforeStartOf(self $range): bool
    {
        return $this->getStart() < $range->getStart();
    }

    public function endsBeforeStartOf(self $range): bool
    {
        return $this->getFinish() < $range->getStart();
    }

    public function endsBeforeFinishOf(self $range): bool
    {
        return $this->getFinish() < $range->getFinish();
    }

    public function intersects(self $range): bool
    {
        return !$this->endsBeforeStartOf($range) && !$range->endsBeforeStartOf($this);
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function sliceBeforeStartOf(self $range): self
    {
        $piece = $this->copyBeforeStartOf($range);
        $this->setStart($range->getStart());
        return $piece;
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function sliceBeforeFinishOf(self $range): self
    {
        $piece = $this->copyBeforeFinishOf($range);
        $this->setStart($range->getFinish() + 1);
        return $piece;
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyBeforeStartOf(self $range): self
    {
        return new self($this->getStart(), $range->getStart() - 1);
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyAfterFinishOf(self $range): self
    {
        return new self($range->getFinish() + 1, $this->getFinish());
    }

    public function containsStartOf(self $range): bool
    {
        return $this->getStart() <= $range->getStart() && $range->getStart() <= $this->getFinish();
    }

    public function containsFinishOf(self $range): bool
    {
        return $this->getStart() <= $range->getFinish() && $range->getFinish() <= $this->getFinish();
    }

    public function follows(self $range): bool
    {
        return $this->getStart() == $range->getFinish() + 1;
    }

    /**
     * @param Range $range
     * @throws Exception
     */
    public function alignStart(self $range)
    {
        $this->setStart($range->getStart());
    }

    /**
     * @param Range $range
     * @throws Exception
     */
    public function alignFinish(self $range)
    {
        $this->setFinish($range->getFinish());
    }

    /**
     * @param Range $range
     * @return Range
     * @throws Exception
     */
    public function copyBeforeFinishOf(self $range): self
    {
        return new self($this->getStart(), $range->getFinish());
    }

    public function export(): array
    {
        return [$this->getStart(), $this->getFinish()];
    }
}
