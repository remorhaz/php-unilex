<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Command;

use Remorhaz\UniLex\Example\Brainfuck\Exception;
use Remorhaz\UniLex\Example\Brainfuck\Runtime;

class SetCommandIndexCommand extends AbstractCommand
{

    private $commandIndex;

    public function __construct(Runtime $runtime, int $index)
    {
        parent::__construct($runtime);
        $this->commandIndex = $index;
    }

    /**
     * @throws Exception
     */
    public function exec(): void
    {
        $this->runtime->setCommandIndex($this->commandIndex);
    }
}
