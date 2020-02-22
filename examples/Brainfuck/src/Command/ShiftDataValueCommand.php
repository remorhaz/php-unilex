<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;

class ShiftDataValueCommand extends AbstractCommand
{

    private $value;

    public function __construct(Runtime $runtime, int $value)
    {
        parent::__construct($runtime);
        $this->value = $value;
    }

    /**
     * @throws Exception
     */
    public function exec(): void
    {
        $this->runtime->shiftDataValue($this->value);
        $this->runtime->shiftCommandIndex(1);
    }
}
