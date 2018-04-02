<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class LanguageMap
{

    /**
     * @var RangeSet[]
     */
    private $symbolMap = [];

    private $transitionMap;

    private $nextSymbolId = 0;

    public function __construct(TransitionMap $transitionMap)
    {
        $this->transitionMap = $transitionMap;
    }

    /**
     * @return RangeSet[]
     */
    public function getSymbolMap(): array
    {
        return $this->symbolMap;
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
        $symbolSplitMap = [];
        $symbolList = [];
        $shouldAddNewSymbol = true;
        foreach ($this->symbolMap as $symbolId => $oldRangeSet) {
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
            $this->symbolMap[$symbolId] = $onlyInOldRangeSet;
            $splitSymbolId = $this->nextSymbolId++;
            $symbolList[] = $splitSymbolId;
            $symbolSplitMap[$symbolId][] = $splitSymbolId;
            $this->symbolMap[$splitSymbolId] = $oldRangeSet->getAnd(...$newRangeSet->getRanges());
            $newRangeSet = $newRangeSet->getAnd(...$rangeSetDiff->getRanges());
        }
        $this->appendSplittedSymbols($symbolSplitMap);
        if ($shouldAddNewSymbol) {
            $newSymbolId = $this->nextSymbolId++;
            $symbolList[] = $newSymbolId;
            $this->symbolMap[$newSymbolId] = $newRangeSet;
        }
        $this->transitionMap->addTransition($stateIn, $stateOut, $symbolList);
    }

    /**
     * @param array $symbolSplitMap
     */
    private function appendSplittedSymbols(array $symbolSplitMap): void
    {
        foreach ($symbolSplitMap as $splitSymbolId => $symbolsToAdd) {
            $addSymbols = function (array $symbolList) use ($splitSymbolId, $symbolsToAdd) {
                return in_array($splitSymbolId, $symbolList)
                    ? array_merge($symbolList, $symbolsToAdd)
                    : $symbolList;
            };
            $this->transitionMap->replaceEachTransition($addSymbols);
        }
    }
}
