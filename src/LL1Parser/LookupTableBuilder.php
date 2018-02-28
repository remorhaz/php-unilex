<?php

namespace Remorhaz\UniLex\LL1Parser;

use Remorhaz\UniLex\Exception;

class LookupTableBuilder
{

    private $terminalMap;

    private $nonTerminalMap;

    private $first = [];

    private $firstHasEpsilon = [];

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
}
