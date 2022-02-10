<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;

abstract class AbstractCommand
{
    protected $runtime;

    protected $index;

    public function __construct(Runtime $runtime)
    {
        $this->runtime = $runtime;
    }

    abstract public function exec(): void;

    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getIndex(): int
    {
        if (!isset($this->index)) {
            throw new Exception("Command index is undefined");
        }
        return $this->index;
    }
}
