<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class Range
{

    private $from;

    private $to;

    public function __construct(int $from, int $to = null)
    {
        // TODO: values validation
        $this->from = $from;
        $this->to = $to ?? $from;
    }

    public function __toString(): string
    {
        return $this->from == $this->to ? "{$this->from}" : "{$this->from}..{$this->to}";
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
}
