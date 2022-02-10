<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;

class OutputCommand extends AbstractCommand
{
    /**
     * @throws Exception
     */
    public function exec(): void
    {
        $this->runtime->outputData();
        $this->runtime->shiftCommandIndex(1);
    }
}
