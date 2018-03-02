<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Grammar\ContextFreeGrammar;

class FirstBuilder
{

    private $grammar;

    private $first;

    public function __construct(ContextFreeGrammar $grammar)
    {
        $this->grammar = $grammar;
    }

    public function getFirst(): FirstInterface
    {
        if (!isset($this->first)) {
            $first = new First;
            $this->addTokensFromTerminalMap($first);
            do {
                $first->resetChangeCount();
                $this->mergeProductionsFromNonTerminalMap($first);
            } while ($first->getChangeCount() > 0);
            $this->first = $first;
        }
        return $this->first;
    }

    private function addTokensFromTerminalMap(First $first): void
    {
        foreach ($this->grammar->getTerminalMap() as $symbolId => $tokenIdList) {
            $first->addToken($symbolId, ...$tokenIdList);
        }
    }

    private function mergeProductionsFromNonTerminalMap(First $first): void
    {
        foreach ($this->grammar->getNonTerminalMap() as $symbolId => $productionList) {
            foreach ($productionList as $production) {
                $first->mergeProductionEpsilons($symbolId, ...$production);
                $first->mergeProductionTokens($symbolId, ...$production);
            }
        }
    }
}
