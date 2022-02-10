<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;

class Production implements StackableSymbolInterface
{
    private $header;

    private $index;

    private $symbolList;

    public function __construct(Symbol $header, int $index, Symbol ...$symbolList)
    {
        $this->header = $header;
        $this->index = $index;
        $this->symbolList = $symbolList;
    }

    public function __toString()
    {
        return "{$this->header->getSymbolId()}:{$this->index}";
    }

    /**
     * @return array|AttributeListShortcut
     */
    public function getHeaderShortcut(): AttributeListShortcut
    {
        return $this->header->getShortcut();
    }

    /**
     * @return array[]|SymbolListShortcut
     */
    public function getSymbolListShortcut(): SymbolListShortcut
    {
        return new SymbolListShortcut($this);
    }

    public function getHeader(): Symbol
    {
        return $this->header;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return Symbol[]
     */
    public function getSymbolList(): array
    {
        return $this->symbolList;
    }

    /**
     * @param int $index
     * @return Symbol
     * @throws Exception
     */
    public function getSymbol(int $index): Symbol
    {
        if (!$this->symbolExists($index)) {
            throw new Exception("Symbol at index {$index} is undefined in production {$this}");
        }
        return $this->symbolList[$index];
    }

    public function isEpsilon(): bool
    {
        return empty($this->getSymbolList());
    }

    public function symbolExists(int $index): bool
    {
        return isset($this->symbolList[$index]);
    }
}
