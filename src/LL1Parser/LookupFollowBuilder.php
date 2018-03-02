<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Grammar\ContextFreeGrammar;

class LookupFollowBuilder
{

    private $grammar;

    private $first;

    private $follow;

    public function __construct(ContextFreeGrammar $grammar, LookupFirstInterface $first)
    {
        $this->grammar = $grammar;
        $this->first = $first;
    }

    public function getFollow(): LookupFollow
    {
        if (!isset($this->follow)) {
            $follow = new LookupFollow();
            $this->addStartSymbol($follow);
            do {
                $follow->resetChangeCount();
                $this->mergeProductionsFromNonTerminalMap($follow);
            } while ($follow->getChangeCount() > 0);
            $this->follow = $follow;
        }
        return $this->follow;
    }

    private function addStartSymbol(LookupFollow $follow): void
    {
        $follow->addToken($this->grammar->getStartSymbol(), $this->grammar->getEofToken());
    }

    private function mergeProductionsFromNonTerminalMap(LookupFollow $follow): void
    {
        foreach ($this->grammar->getNonTerminalMap() as $symbolId => $productionList) {
            foreach ($productionList as $symbolIdList) {
                $this->mergeProduction($follow, $symbolId, ...$symbolIdList);
            }
        }
    }

    private function mergeProduction(LookupFollow $follow, int $nonTerminalId, int ...$nonTerminalIdList): void
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
