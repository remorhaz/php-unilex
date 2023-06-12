<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\IntRangeSets\RangeSetInterface;
use Remorhaz\UniLex\Exception;

class SymbolTable
{
    /**
     * @var array<int, RangeSetInterface>
     */
    private array $rangeSetList = [];

    private int $nextSymbol = 0;

    public function addSymbol(RangeSetInterface $rangeSet): int
    {
        $symbolId = $this->nextSymbol++;
        $this->rangeSetList[$symbolId] = $rangeSet;

        return $symbolId;
    }

    /**
     * @throws Exception
     */
    public function importSymbol(int $symbolId, RangeSetInterface $rangeSet): void
    {
        $this->rangeSetList[$symbolId] = isset($this->rangeSetList[$symbolId])
            ? throw new Exception("Symbol $symbolId already defined in symbol table")
            : $rangeSet;
        $this->nextSymbol = max(array_keys($this->rangeSetList));
    }

    /**
     * @throws Exception
     */
    public function replaceSymbol(int $symbolId, RangeSetInterface $rangeSet): self
    {
        $this->rangeSetList[$symbolId] = isset($this->rangeSetList[$symbolId])
            ? $rangeSet
            : throw new Exception("Symbol {$symbolId} is not defined in symbol table");

        return $this;
    }

    /**
     * @throws Exception
     */
    public function getRangeSet(int $symbolId): RangeSetInterface
    {
        return $this->rangeSetList[$symbolId]
            ?? throw new Exception("Symbol $symbolId is not defined in symbol table");
    }

    /**
     * @return array<int, RangeSetInterface>
     */
    public function getRangeSetList(): array
    {
        return $this->rangeSetList;
    }

    /**
     * @return list<int>
     */
    public function getSymbolList(): array
    {
        return array_keys($this->rangeSetList);
    }
}
