<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\IntRangeSets\RangeSetInterface;
use Remorhaz\UniLex\Exception;

class SymbolTable
{
    /**
     * @var RangeSetInterface[]
     */
    private $rangeSetList = [];

    private $nextSymbol = 0;

    /**
     * @param RangeSetInterface $rangeSet
     * @return int
     */
    public function addSymbol(RangeSetInterface $rangeSet): int
    {
        $symbolId = $this->nextSymbol++;
        $this->rangeSetList[$symbolId] = $rangeSet;
        return $symbolId;
    }

    /**
     * @param int $symbolId
     * @param RangeSetInterface $rangeSet
     * @throws Exception
     */
    public function importSymbol(int $symbolId, RangeSetInterface $rangeSet): void
    {
        if (isset($this->rangeSetList[$symbolId])) {
            throw new Exception("Symbol {$symbolId} already defined in symbol table");
        }
        $this->rangeSetList[$symbolId] = $rangeSet;
        $this->nextSymbol = max(array_keys($this->rangeSetList));
    }

    /**
     * @param int $symbolId
     * @param RangeSetInterface $rangeSet
     * @return SymbolTable
     * @throws Exception
     */
    public function replaceSymbol(int $symbolId, RangeSetInterface $rangeSet): self
    {
        if (!isset($this->rangeSetList[$symbolId])) {
            throw new Exception("Symbol {$symbolId} is not defined in symbol table");
        }
        $this->rangeSetList[$symbolId] = $rangeSet;
        return $this;
    }

    /**
     * @param int $symbolId
     * @return RangeSetInterface
     * @throws Exception
     */
    public function getRangeSet(int $symbolId): RangeSetInterface
    {
        if (!isset($this->rangeSetList[$symbolId])) {
            throw new Exception("Symbol {$symbolId} is not defined in symbol table");
        }
        return $this->rangeSetList[$symbolId];
    }

    /**
     * @return RangeSetInterface[]
     */
    public function getRangeSetList(): array
    {
        return $this->rangeSetList;
    }

    /**
     * @return int[]
     */
    public function getSymbolList(): array
    {
        return array_keys($this->rangeSetList);
    }
}
