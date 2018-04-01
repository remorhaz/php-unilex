<?php

namespace Remorhaz\UniLex\RegExp\FSM;

class LanguageMap
{

    /**
     * @var RangeSet[]
     */
    private $languageMap = [];

    private $transitionMap;

    private $stateMap;

    private $nextSymbolId = 0;

    public function __construct(StateMapInterface $stateMap)
    {
        $this->stateMap = $stateMap;
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
        foreach ($this->languageMap as $symbolId => $oldRangeSet) {
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
            $this->languageMap[$symbolId] = $onlyInOldRangeSet;
            $splitSymbolId = $this->nextSymbolId++;
            $symbolList[] = $splitSymbolId;
            $symbolSplitMap[$symbolId][] = $splitSymbolId;
            $newRangeSet = $newRangeSet->getAnd(...$rangeSetDiff->getRanges());
        }
        $this->appendSplittedSymbols($symbolSplitMap);
        if ($shouldAddNewSymbol) {
            $newSymbolId = $this->nextSymbolId++;
            $symbolList[] = $newSymbolId;
            $this->languageMap[$newSymbolId] = $newRangeSet;
        }
        $this->getTransitionMap()->addTransition($stateIn, $stateOut, $symbolList);
    }

    /**
     * @param array $symbolSplitMap
     * @throws \Remorhaz\UniLex\Exception
     */
    private function appendSplittedSymbols(array $symbolSplitMap): void
    {
        foreach ($symbolSplitMap as $splitSymbolId => $symbolsToAdd) {
            foreach ($this->getTransitionMap()->getTransitionList() as $stateIn => $stateOutMap) {
                foreach ($stateOutMap as $stateOut => $symbolList) {
                    if (in_array($splitSymbolId, $symbolList)) {
                        $symbolList = array_merge($symbolList, $symbolsToAdd);
                        $this
                            ->getTransitionMap()
                            ->replaceTransition($stateIn, $stateOut, $symbolList);
                    }
                }
            }
        }
    }

    /**
     * @param int $stateIn
     * @param int $stateOut
     * @return mixed
     * @throws \Remorhaz\UniLex\Exception
     */
    public function getTransition(int $stateIn, int $stateOut)
    {
        return $this
            ->getTransitionMap()
            ->getTransition($stateIn, $stateOut);
    }

    /**
     * @return int[]
     */
    public function getTranslationList(): array
    {
        return $this
            ->getTransitionMap()
            ->getTransitionList();
    }

    private function getTransitionMap(): TransitionMap
    {
        if (!isset($this->transitionMap)) {
            $this->transitionMap = new TransitionMap($this->stateMap);
        }
        return $this->transitionMap;
    }
}
