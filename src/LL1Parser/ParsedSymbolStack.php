<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;

class ParsedSymbolStack
{

    /**
     * @var ParsedSymbol[]
     */
    private $data = [];

    /**
     * @return ParsedSymbol
     * @throws Exception
     */
    public function pop(): ParsedSymbol
    {
        if (empty($this->data)) {
            throw new Exception("Unexpected end of stack");
        }
        return array_pop($this->data);
    }

    public function push(ParsedSymbol ...$symbolList): void
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
