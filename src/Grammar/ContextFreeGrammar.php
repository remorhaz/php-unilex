<?php

namespace Remorhaz\UniLex\Grammar;

class ContextFreeGrammar
{

    private $terminalMap;

    private $nonTerminalMap;

    private $startSymbol;

    private $eoiSymbol;

    /**
     * Constructor. Accepts non-empty maps of terminal and non-terminal productions separately.
     *
     * @param array $terminalMap Production IDs as keys, non-empty arrays of token IDs as values.
     * @param array $nonTerminalMap Production IDs as keys, non-empty arrays of arrays of production IDs as values.
     * @param int $startSymbol
     * @param int $eoiSymbol
     */
    public function __construct(array $terminalMap, array $nonTerminalMap, int $startSymbol, int $eoiSymbol)
    {
        $this->terminalMap = $terminalMap;
        $this->nonTerminalMap = $nonTerminalMap;
        $this->startSymbol = $startSymbol;
        $this->eoiSymbol = $eoiSymbol;
    }

    public function getTerminalMap(): array
    {
        return $this->terminalMap;
    }

    public function getNonTerminalMap(): array
    {
        return $this->nonTerminalMap;
    }

    public function getStartSymbol(): int
    {
        return $this->startSymbol;
    }

    public function getEoiSymbol(): int
    {
        return $this->eoiSymbol;
    }
}
