<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class Range
{

    private $from;

    private $to;

    /**
     * @param int $from
     * @param int|null $to
     * @throws Exception
     */
    public function __construct(int $from, int $to = null)
    {
        if (isset($to) && $from > $to) {
            throw new Exception("Invalid exception {$from}..{$to}");
        }
        $this->from = $from;
        $this->to = $to ?? $from;
    }

    public function __toString(): string
    {
        return $this->from == $this->to ? "{$this->from}" : "{$this->from}..{$this->to}";
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

    public function getFrom(): int
    {
        return $this->from;
    }

    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @param int $from
     * @throws Exception
     */
    public function setFrom(int $from): void
    {
        if ($from > $this->getTo()) {
            throw new Exception("Invalid range {$this}");
        }

        $this->from = $from;
    }

    /**
     * @param int $to
     * @throws Exception
     */
    public function setTo(int $to): void
    {
        if ($this->getFrom() > $to) {
            throw new Exception("Invalid range {$this}");
        }
        $this->to = $to;
    }

    public function containsChar(int $char): bool
    {
        return $this->getFrom() <= $char && $char <= $this->getTo();
    }

    public function export(): array
    {
        return [$this->getFrom(), $this->getTo()];
    }
}
