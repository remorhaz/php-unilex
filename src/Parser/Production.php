<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Stack\StackableSymbolInterface;
use Stringable;

class Production implements StackableSymbolInterface, Stringable
{
    /**
     * @var list<Symbol>
     */
    private array $symbolList;

    public function __construct(
        private readonly Symbol $header,
        private readonly int $index,
        Symbol ...$symbolList,
    ) {
        $this->symbolList = \array_values($symbolList);
    }

    public function __toString(): string
    {
        return "{$this->header->getSymbolId()}:$this->index";
    }

    public function getHeaderShortcut(): AttributeListShortcut
    {
        return $this->header->getShortcut();
    }

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
     * @return list<Symbol>
     */
    public function getSymbolList(): array
    {
        return $this->symbolList;
    }

    /**
     * @throws Exception
     */
    public function getSymbol(int $index): Symbol
    {
        return $this->symbolList[$index]
            ?? throw new Exception("Symbol at index $index is undefined in production $this");
    }

    public function isEpsilon(): bool
    {
        return empty($this->symbolList);
    }

    public function symbolExists(int $index): bool
    {
        return isset($this->symbolList[$index]);
    }
}
