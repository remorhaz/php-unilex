<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Grammar\ContextFreeGrammar;

class FollowBuilder
{

    private $grammar;

    private $first;

    private $follow;

    public function __construct(ContextFreeGrammar $grammar, FirstInterface $first)
    {
        $this->grammar = $grammar;
        $this->first = $first;
    }

    /**
     * @return Follow
     * @throws \Remorhaz\UniLex\Exception
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
     * @throws \Remorhaz\UniLex\Exception
     */
    private function mergeProductionsFromNonTerminalMap(Follow $follow): void
    {
        foreach ($this->grammar->getFullProductionList() as [$symbolId, $production]) {
            $this->mergeProduction($follow, $symbolId, ...$production);
        }
    }

    private function mergeProduction(Follow $follow, int $nonTerminalId, int ...$nonTerminalIdList): void
    {
        while (!empty($nonTerminalIdList)) {
            $symbolId = array_shift($nonTerminalIdList);
            $rightPartFirst = $this->first->getProductionTokens(...$nonTerminalIdList);
            $follow->addToken($symbolId, ...$rightPartFirst);
            if ($this->first->productionHasEpsilon(...$nonTerminalIdList)) {
                $follow->mergeTokens($symbolId, $nonTerminalId);
            }
        };
    }
}
