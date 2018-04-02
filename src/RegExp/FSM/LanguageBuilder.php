<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class LanguageBuilder
{

    private $symbolTable;

    private $transitionMap;

    public function __construct(SymbolTable $symbolTable, TransitionMap $transitionMap)
    {
        $this->symbolTable = $symbolTable;
        $this->transitionMap = $transitionMap;
    }

    /**
     * @param int $stateIn
     * @param int $stateOut
     * @param Range ...$ranges
     * @throws \Remorhaz\UniLex\Exception
     */
    public function addTransition(int $stateIn, int $stateOut, Range ...$ranges): void
    {
        $newRangeSet = new RangeSet(...$ranges);
        $symbolList = [];
        $shouldAddNewSymbol = true;
        foreach ($this->symbolTable->getRangeSetList() as $symbolId => $oldRangeSet) {
            $rangeSetDiff = $oldRangeSet->getDiff(...$newRangeSet->getRanges());
            if ($rangeSetDiff->isEmpty()) { // same range sets
                $symbolList[] = $symbolId;
                $shouldAddNewSymbol = false;
                break;
            }
            $onlyInOldRangeSet = $oldRangeSet->getAnd(...$rangeSetDiff->getRanges());
            if ($onlyInOldRangeSet->isSame(...$oldRangeSet->getRanges())) { // range sets don't intersect
                continue;
            }
            $splitSymbolId = $this
                ->symbolTable
                ->replaceSymbol($symbolId, $onlyInOldRangeSet)
                ->addSymbol($oldRangeSet->getAnd(...$newRangeSet->getRanges()));
            $this->splitSymbolInTransitions($symbolId, $splitSymbolId);
            $symbolList[] = $splitSymbolId;
            $newRangeSet = $newRangeSet->getAnd(...$rangeSetDiff->getRanges());
        }
        if ($shouldAddNewSymbol) {
            $newSymbolId = $this->symbolTable->addSymbol($newRangeSet);
            $symbolList[] = $newSymbolId;
        }
        $this->transitionMap->addTransition($stateIn, $stateOut, $symbolList);
    }

    private function splitSymbolInTransitions(int $symbolId, int $symbolToAdd): void
    {
        $addSymbol = function (array $symbolList) use ($symbolId, $symbolToAdd) {
            if (in_array($symbolId, $symbolList)) {
                $symbolList[] = $symbolToAdd;
            }
            return $symbolList;
        };
        $this->transitionMap->replaceEachTransition($addSymbol);
    }
}
