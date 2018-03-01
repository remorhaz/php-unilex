<?php

namespace Remorhaz\UniLex\LL1Parser;

class LookupFollowBuilder
{

    private $grammar;

    private $first;

    private $follow;

    public function __construct(Grammar $grammar, LookupFirstInfoInterface $first)
    {
        $this->grammar = $grammar;
        $this->first = $first;
    }

    public function getFollow(): LookupFollow
    {
        if (!isset($this->follow)) {
            $follow = new LookupFollow($this->first);
            $this->addStartSymbol($follow);
            // @todo Extract repeatUntilNoChanges(Callable $func)
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
        foreach ($this->grammar->getNonTerminalMap() as $nonTerminalId => $productionList) {
            foreach ($productionList as $nonTerminalIdList) {
                $this->mergeProduction($follow, $nonTerminalId, ...$nonTerminalIdList);
            }
        }
    }

    private function mergeProduction(LookupFollow $follow, int $nonTerminalId, int ...$nonTerminalIdList): void
    {
        while (!empty($nonTerminalIdList)) {
            $symbolId = array_shift($nonTerminalIdList);
            $follow->mergeTokensFromFirst($this->first, $symbolId, $nonTerminalIdList);
            if ($this->first->hasEpsilon($nonTerminalIdList)) {
                $follow->mergeTokens($symbolId, $nonTerminalId);
            }
        };
    }
}
