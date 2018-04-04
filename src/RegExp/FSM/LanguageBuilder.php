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
        $rangeSetCalc = new RangeSetCalc;
        $newRangeSet = new RangeSet(...$ranges);
        $symbolList = [];
        $shouldAddNewSymbol = true;
        foreach ($this->symbolTable->getRangeSetList() as $symbolId => $oldRangeSet) {
            if ($rangeSetCalc->equals($oldRangeSet, $newRangeSet)) {
                $symbolList[] = $symbolId;
                $shouldAddNewSymbol = false;
                break;
            }
            $rangeSetDiff = $rangeSetCalc->xor($oldRangeSet, $newRangeSet);
            $onlyInOldRangeSet = $rangeSetCalc->and($oldRangeSet, $rangeSetDiff);
            if ($rangeSetCalc->equals($onlyInOldRangeSet, $oldRangeSet)) {
                continue;
            }
            $splitSymbolId = $this
                ->symbolTable
                ->replaceSymbol($symbolId, $onlyInOldRangeSet)
                ->addSymbol($rangeSetCalc->and($oldRangeSet, $newRangeSet));
            $this->splitSymbolInTransitions($symbolId, $splitSymbolId);
            $symbolList[] = $splitSymbolId;
            $newRangeSet = $rangeSetCalc->and($newRangeSet, $rangeSetDiff);
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
