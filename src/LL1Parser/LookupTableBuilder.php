<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\CFG\Grammar;
use Remorhaz\UniLex\Exception;

class LookupTableBuilder
{

    private $grammar;

    private $first;

    private $follow;

    private $table;

    public function __construct(Grammar $grammar)
    {
        $this->grammar = $grammar;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getTable(): array
    {
        if (!isset($this->table)) {
            $table = [];
            foreach ($this->grammar->getNonTerminalMap() as $productionId => $productionList) {
                foreach ($productionList as $symbolId => $symbolIdList) {
                    $productionFirsts = $this->getFirst()->getProductionTokens(...$symbolIdList);
                    foreach ($productionFirsts as $tokenId) {
                        if (isset($table[$productionId][$tokenId])) {
                            throw new Exception("Lookup table cell {$productionId}:{$tokenId} is already set");
                        }
                        $table[$productionId][$tokenId] = $symbolIdList;
                    }
                    if (!$this->getFirst()->productionHasEpsilon(...$symbolIdList)) {
                        continue;
                    }
                    $productionFollows = $this->getFollow()->getTokens($productionId);
                    foreach ($productionFollows as $tokenId) {
                        if (isset($table[$productionId][$tokenId])) {
                            throw new Exception("Lookup table cell {$productionId}:{$tokenId} is already set");
                        }
                        $table[$productionId][$tokenId] = $symbolIdList;
                    }
                }
            }
            $this->table = $table;
        }
        return $this->table;
    }

    private function getFirst(): LookupFirstInfoInterface
    {
        if (!isset($this->first)) {
            $builder = new LookupFirstBuilder($this->grammar);
            $this->first = $builder->getFirst();
        }
        return $this->first;
    }

    private function getFollow(): LookupFollowInfoInterface
    {
        if (!isset($this->follow)) {
            $builder = new LookupFollowBuilder($this->grammar, $this->getFirst());
            $this->follow = $builder->getFollow();
        }
        return $this->follow;
    }
}
