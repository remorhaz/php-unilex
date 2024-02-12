<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Grammar\ContextFree\GrammarInterface;

class FirstBuilder
{
    private ?FirstInterface $first = null;

    public function __construct(
        private readonly GrammarInterface $grammar,
    ) {
    }

    /**
     * @return FirstInterface
     */
    public function getFirst(): FirstInterface
    {
        return $this->first ??= $this->buildFirst();
    }

    private function buildFirst(): FirstInterface
    {
        $first = new First();
        $this->addTokensFromTerminalMap($first);
        do {
            $first->resetChangeCount();
            $this->mergeProductionsFromNonTerminalMap($first);
        } while ($first->getChangeCount() > 0);

        return $first;
    }

    /**
     * @param First $first
     */
    private function addTokensFromTerminalMap(First $first): void
    {
        foreach ($this->grammar->getTerminalList() as $symbolId) {
            $tokenId = $this->grammar->getToken($symbolId);
            $first->addToken($symbolId, $tokenId);
        }
    }

    /**
     * @param First $first
     */
    private function mergeProductionsFromNonTerminalMap(First $first): void
    {
        foreach ($this->grammar->getFullProductionList() as $production) {
            $first->mergeProductionEpsilons($production->getHeaderId(), ...$production->getSymbolList());
            $first->mergeProductionTokens($production->getHeaderId(), ...$production->getSymbolList());
        }
    }
}
