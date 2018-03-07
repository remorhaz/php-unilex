<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\Production;

class FollowBuilder
{

    private $grammar;

    private $first;

    private $follow;

    public function __construct(GrammarInterface $grammar, FirstInterface $first)
    {
        $this->grammar = $grammar;
        $this->first = $first;
    }

    /**
     * @return Follow
     */
    public function getFollow(): Follow
    {
        if (!isset($this->follow)) {
            $follow = new Follow();
            $this->addStartSymbol($follow);
            do {
                $follow->resetChangeCount();
                $this->mergeProductionsFromNonTerminalMap($follow);
            } while ($follow->getChangeCount() > 0);
            $this->follow = $follow;
        }
        return $this->follow;
    }

    private function addStartSymbol(Follow $follow): void
    {
        $follow->addToken($this->grammar->getStartSymbol(), $this->grammar->getEoiSymbol());
    }

    /**
     * @param Follow $follow
     */
    private function mergeProductionsFromNonTerminalMap(Follow $follow): void
    {
        foreach ($this->grammar->getFullProductionList() as $production) {
            $this->mergeProduction($follow, $production);
        }
    }

    private function mergeProduction(Follow $follow, Production $production): void
    {
        $symbolIdList = $production->getSymbolList();
        while (!empty($symbolIdList)) {
            $targetSymbolId = array_shift($symbolIdList);
            $rightPartFirst = $this->first->getProductionTokens(...$symbolIdList);
            $follow->addToken($targetSymbolId, ...$rightPartFirst);
            if ($this->first->productionHasEpsilon(...$symbolIdList)) {
                $follow->mergeTokens($targetSymbolId, $production->getSymbolId());
            }
        };
    }
}
