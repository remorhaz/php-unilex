<?php

namespace Remorhaz\UniLex\LL1Parser;

class LookupFollow extends LookupSet
{

    private $first;

    public function __construct(LookupFirstInfoInterface $first)
    {
        $this->first = $first;
    }

    public function get(int $nonTerminalId): array
    {
        return $this->getOne($nonTerminalId);
    }

    public function mergeTokens(int $targetNonTerminalId, int $sourceNonTerminalId): void
    {
        $this->addToken($targetNonTerminalId, ...$this->get($sourceNonTerminalId));
    }

    public function mergeTokensFromFirst(
        LookupFirstInfoInterface $first,
        int $targetNonTerminalId,
        int ...$sourceNonTerminalIdList
    ) {
        $this->addToken($targetNonTerminalId, ...$first->get(...$sourceNonTerminalIdList));
    }
}
