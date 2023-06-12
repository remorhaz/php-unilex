<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;

abstract class AbstractCommand
{
    protected ?int $index = null;

    public function __construct(protected Runtime $runtime)
    {
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
        return $this->index ?? throw new Exception("Command index is undefined");
    }
}
