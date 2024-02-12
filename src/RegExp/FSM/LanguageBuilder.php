<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\RegExp\FSM;

use Remorhaz\IntRangeSets\RangeInterface;
use Remorhaz\IntRangeSets\RangeSet;
use Remorhaz\UniLex\Exception as UniLexException;

class LanguageBuilder
{
    public function __construct(
        private readonly SymbolTable $symbolTable,
        private readonly TransitionMap $transitionMap,
    ) {
    }

    public static function forNfa(Nfa $nfa): self
    {
        return new self($nfa->getSymbolTable(), $nfa->getSymbolTransitionMap());
    }

    /**
     * @throws UniLexException
     */
    public function addTransition(int $stateIn, int $stateOut, RangeInterface ...$ranges): void
    {
        $this->transitionMap->addTransition($stateIn, $stateOut, $this->getSymbolList(...$ranges));
    }

    /**
     * @param RangeInterface ...$ranges
     * @return list<int>
     * @throws UniLexException
     */
    public function getSymbolList(RangeInterface ...$ranges): array
    {
        $newRangeSet = RangeSet::create(...$ranges);
        $symbolList = [];
        $shouldAddNewSymbol = true;
        foreach ($this->symbolTable->getRangeSetList() as $symbolId => $oldRangeSet) {
            if ($oldRangeSet->equals($newRangeSet)) {
                $symbolList[] = $symbolId;
                $shouldAddNewSymbol = false;
                break;
            }
            $rangeSetDiff = $oldRangeSet->createSymmetricDifference($newRangeSet);
            $onlyInOldRangeSet = $oldRangeSet->createIntersection($rangeSetDiff);
            if ($onlyInOldRangeSet->isEmpty()) {
                $symbolList[] = $symbolId;
                $newRangeSet = $rangeSetDiff;
                continue;
            }
            if ($onlyInOldRangeSet->equals($oldRangeSet)) {
                continue;
            }
            $splitSymbolId = $this
                ->symbolTable
                ->replaceSymbol($symbolId, $onlyInOldRangeSet)
                ->addSymbol($and = $oldRangeSet->createIntersection($newRangeSet));
            $this->splitSymbolInTransitions($symbolId, $splitSymbolId);
            $symbolList[] = $splitSymbolId;
            $newRangeSet = $newRangeSet->createIntersection($rangeSetDiff);
        }
        if ($shouldAddNewSymbol && !$newRangeSet->isEmpty()) {
            $newSymbolId = $this->symbolTable->addSymbol($newRangeSet);
            $symbolList[] = $newSymbolId;
        }

        return $symbolList;
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
