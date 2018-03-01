<?php

namespace Remorhaz\UniLex\LL1Parser;

/**
 * @todo Move to Grammar?
 */
class LookupFirstBuilder
{

    private $grammar;

    private $first;

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    public function getFirst(): LookupFirstInfoInterface
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
        foreach ($this->grammar->getTerminalMap() as $nonTerminalId => $tokenIdList) {
            $first->addToken($nonTerminalId, ...$tokenIdList);
        }
    }

    private function mergeProductionsFromNonTerminalMap(LookupFirst $first): void
    {
        foreach ($this->grammar->getNonTerminalMap() as $nonTerminalId => $productionList) {
            foreach ($productionList as $production) {
                $first->mergeEpsilons($nonTerminalId, ...$production);
                $first->mergeTokens($nonTerminalId, ...$production);
            }
        }
    }
}
