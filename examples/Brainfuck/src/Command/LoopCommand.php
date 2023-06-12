<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;

class LoopCommand extends AbstractCommand
{
    private ?int $endLoopIndex = null;

    /**
     * @throws Exception
     */
    public function exec(): void
    {
        if ($this->runtime->isValueZero()) {
            $this->runtime->setCommandIndex($this->getEndLoopIndex() + 1);
            return;
        }
        $this->runtime->shiftCommandIndex(1);
    }

    public function setEndLoopIndex(int $index): void
    {
        $this->endLoopIndex = $index;
    }

    /**
     * @throws Exception
     */
    private function getEndLoopIndex(): int
    {
        return $this->endLoopIndex ?? throw new Exception("End loop index is not defined");
    }
}
