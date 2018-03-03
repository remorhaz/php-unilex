<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;

class FirstBuilder
{

    private $grammar;

    private $first;

    public function __construct(GrammarInterface $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * @return FirstInterface
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
     */
    private function mergeProductionsFromNonTerminalMap(First $first): void
    {
        foreach ($this->grammar->getFullProductionList() as [$symbolId, $production]) {
            $first->mergeProductionEpsilons($symbolId, ...$production);
            $first->mergeProductionTokens($symbolId, ...$production);
        }
    }
}
