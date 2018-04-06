<?php

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\UniLex\Exception;

class SymbolTable
{

    /**
     * @var RangeSet[]
     */
    private $rangeSetList = [];

    private $nextSymbol = 0;

    /**
     * @param RangeSet $rangeSet
     * @return int
     */
    public function addSymbol(RangeSet $rangeSet): int
    {
        $symbolId = $this->nextSymbol++;
        $this->rangeSetList[$symbolId] = $rangeSet;
        return $symbolId;
    }

    /**
     * @param int $symbolId
     * @param RangeSet $rangeSet
     * @throws Exception
     */
    public function importSymbol(int $symbolId, RangeSet $rangeSet): void
    {
        if (isset($this->rangeSetList[$symbolId])) {
            throw new Exception("Symbol {$symbolId} already defined in symbol table");
        }
        $this->rangeSetList[$symbolId] = $rangeSet;
        $this->nextSymbol = max(array_keys($this->rangeSetList));
    }

    /**
     * @param int $symbolId
     * @param RangeSet $rangeSet
     * @return SymbolTable
     * @throws Exception
     */
    public function replaceSymbol(int $symbolId, RangeSet $rangeSet): self
    {
        if (!isset($this->rangeSetList[$symbolId])) {
            throw new Exception("Symbol {$symbolId} is not defined in symbol table");
        }
        $this->rangeSetList[$symbolId] = $rangeSet;
        return $this;
    }

    /**
     * @param int $symbolId
     * @return RangeSet
     * @throws Exception
     */
    public function getRangeSet(int $symbolId): RangeSet
    {
        if (!isset($this->rangeSetList[$symbolId])) {
            throw new Exception("Symbol {$symbolId} is not defined in symbol table");
        }
        return $this->rangeSetList[$symbolId];
    }

    /**
     * @return RangeSet[]
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
