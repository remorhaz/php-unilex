<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Stack;

use Remorhaz\UniLex\Exception;

class SymbolStack implements PushInterface
{
    /**
     * @var list<StackableSymbolInterface>
     */
    private array $data = [];

    /**
     * @throws Exception
     */
    public function pop(): StackableSymbolInterface
    {
        if (empty($this->data)) {
            throw new Exception("Unexpected end of stack");
        }

        return array_pop($this->data);
    }

    public function push(StackableSymbolInterface ...$symbolList): void
    {
        if (empty($symbolList)) {
            return;
        }

        array_push($this->data, ...array_reverse($symbolList));
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
