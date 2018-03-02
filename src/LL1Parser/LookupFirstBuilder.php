<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\CFG\Grammar;

class LookupFirstBuilder
{

    private $grammar;

    private $first;

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    public function getFirst(): LookupFirstInterface
    {
        if (!isset($this->first)) {
            $first = new LookupFirst;
            $this->addTokensFromTerminalMap($first);
            do {
                $first->resetChangeCount();
                $this->mergeProductionsFromNonTerminalMap($first);
            } while ($first->getChangeCount() > 0);
            $this->first = $first;
        }
        return $this->first;
    }

    private function addTokensFromTerminalMap(LookupFirst $first): void
    {
        foreach ($this->grammar->getTerminalMap() as $symbolId => $tokenIdList) {
            $first->addToken($symbolId, ...$tokenIdList);
        }
    }

    private function mergeProductionsFromNonTerminalMap(LookupFirst $first): void
    {
        foreach ($this->grammar->getNonTerminalMap() as $symbolId => $productionList) {
            foreach ($productionList as $production) {
                $first->mergeProductionEpsilons($symbolId, ...$production);
                $first->mergeProductionTokens($symbolId, ...$production);
            }
        }
    }
}
