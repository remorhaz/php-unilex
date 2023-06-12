<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;
use Remorhaz\UniLex\Grammar\ContextFree\Production;

class FollowBuilder
{
    private ?FollowInterface $follow = null;

    public function __construct(
        private GrammarInterface $grammar,
        private FirstInterface $first,
    ) {
    }

    /**
     * @return Follow
     */
    public function getFollow(): FollowInterface
    {
        return $this->follow ??= $this->buildFollow();
    }

    private function buildFollow(): FollowInterface
    {
        $follow = new Follow();
        $this->addStartSymbol($follow);
        do {
            $follow->resetChangeCount();
            $this->mergeProductionsFromNonTerminalMap($follow);
        } while ($follow->getChangeCount() > 0);

        return $follow;
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
                $follow->mergeTokens($targetSymbolId, $production->getHeaderId());
            }
        };
    }
}
