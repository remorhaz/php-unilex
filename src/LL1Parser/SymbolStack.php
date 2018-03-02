<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;

class SymbolStack
{

    private $data = [];

    /**
     * @return int
     * @throws Exception
     */
    public function pop(): int
    {
        if (empty($this->data)) {
            throw new Exception("Unexpected end of stack");
        }
        return array_pop($this->data);
    }

    public function push(int ...$symbolIdList): void
    {
        if (empty($symbolIdList)) {
            return;
        }
        array_push($this->data, ...array_reverse($symbolIdList));
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function reset(): void
    {
        $this->data = [];
    }
}
