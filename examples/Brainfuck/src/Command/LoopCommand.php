<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;

class LoopCommand extends AbstractCommand
{

    private $endLoopIndex;

    public function __construct(Runtime $runtime)
    {
        parent::__construct($runtime);
    }

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
     * @return int
     * @throws Exception
     */
    private function getEndLoopIndex(): int
    {
        if (!isset($this->endLoopIndex)) {
            throw new Exception("End loop index is not defined");
        }
        return $this->endLoopIndex;
    }
}
