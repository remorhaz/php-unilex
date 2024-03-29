<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;

class ShiftDataIndexCommand extends AbstractCommand
{
    public function __construct(
        Runtime $runtime,
        private int $dataIndex,
    ) {
        parent::__construct($runtime);
    }

    /**
     * @throws Exception
     */
    public function exec(): void
    {
        $this->runtime->shiftDataIndex($this->dataIndex);
        $this->runtime->shiftCommandIndex(1);
    }
}
