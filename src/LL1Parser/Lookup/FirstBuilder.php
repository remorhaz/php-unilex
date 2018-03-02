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

    /**
     * @return FirstInterface
     * @throws \Remorhaz\UniLex\Exception
     */
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

    /**
     * @param First $first
     * @throws \Remorhaz\UniLex\Exception
     */
    private function addTokensFromTerminalMap(First $first): void
    {
        foreach ($this->grammar->getTerminalList() as $symbolId) {
            $tokenIdList = $this->grammar->getTerminalTokenList($symbolId);
            $first->addToken($symbolId, ...$tokenIdList);
        }
    }

    /**
     * @param First $first
     * @throws \Remorhaz\UniLex\Exception
     */
    private function mergeProductionsFromNonTerminalMap(First $first): void
    {
        foreach ($this->grammar->getNonTerminalList() as $symbolId) {
            foreach ($this->grammar->getProductionList($symbolId) as $production) {
                $first->mergeProductionEpsilons($symbolId, ...$production);
                $first->mergeProductionTokens($symbolId, ...$production);
            }
        }
    }
}
