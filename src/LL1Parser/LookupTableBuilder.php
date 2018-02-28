<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;

class LookupTableBuilder
{

    private $terminalMap;

    private $nonTerminalMap;

    /**
     * Constructor. Accepts non-empty maps of terminal and non-terminal productions separately.
     *
     * @param array $terminalMap Production IDs as keys, non-empty arrays of token IDs as values.
     * @param array $nonTerminalMap Production IDs as keys, non-empty arrays of arrays of production IDs as values.
     * @throws Exception
     */
    public function __construct(array $terminalMap, array $nonTerminalMap)
    {
        if (empty($terminalMap)) {
            throw new Exception("Empty map of terminal productions");
        }
        if (empty($nonTerminalMap)) {
            throw new Exception("Empty map of non-terminal productions");
        }
        $this->terminalMap = $terminalMap;
        $this->nonTerminalMap = $nonTerminalMap;
    }

    /**
     * @throws Exception
     * @todo Make private?
     */
    public function validateMaps(): void
    {
        $mapIntersection = array_intersect_key($this->nonTerminalMap, $this->terminalMap);
        if (!empty($mapIntersection)) {
            $keyList = implode(", ", array_keys($mapIntersection));
            throw new Exception("Productions marked both as terminals and non-terminals: {$keyList}");
        }
    }

    public function build(): void
    {
        $first = new LookupFirst;
        $this->addTerminalsToFirst($first);
        do {
            $first->resetChangeCount();
            $this->addNonTerminalsToFirst($first);
        } while ($first->getChangeCount() > 0);
    }

    private function addTerminalsToFirst(LookupFirst $first): void
    {
        foreach ($this->terminalMap as $terminalId => $tokenIdList) {
            $first->add($terminalId, ...$tokenIdList);
        }
    }

    private function addNonTerminalsToFirst(LookupFirst $first): void
    {
        foreach ($this->nonTerminalMap as $nonTerminalId => $productionList) {
            foreach ($productionList as $production) {
                $this->addProductionToFirst($first, $nonTerminalId, $production);
            }
        }
    }

    private function addProductionToFirst(LookupFirst $first, int $nonTerminalId, array $production): void
    {
        if (empty($production) || $first->hasEpsilon(...$production)) {
            $first->addEpsilon($nonTerminalId);
        }
        foreach ($production as $i => $productionId) {
            $first->merge($productionId, $nonTerminalId);
            if (!$first->hasEpsilon($productionId)) {
                break;
            }
        }
    }
}
