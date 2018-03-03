<?php

namespace Remorhaz\UniLex\Grammar;

use Generator;
use Remorhaz\UniLex\Exception;

class ContextFreeGrammar implements ContextFreeGrammarInterface
{

    private $terminalMap = [];

    private $nonTerminalMap = [];

    private $startSymbol;

    private $eoiSymbol;

    /**
     * Constructor. Accepts non-empty maps of terminal and non-terminal productions separately.
     *
     * @param int $startSymbol
     * @param int $eoiSymbol
     */
    public function __construct(int $startSymbol, int $eoiSymbol)
    {
        $this->startSymbol = $startSymbol;
        $this->eoiSymbol = $eoiSymbol;
    }

    /**
     * @param int $symbolId
     * @param int[] ...$tokenIdList
     */
    public function addToken(int $symbolId, int ...$tokenIdList): void
    {
        if (empty($tokenIdList)) {
            return;
        }
        $this->terminalMap[$symbolId] = array_merge(
            $this->terminalMap[$symbolId] ?? [],
            $tokenIdList
        );
    }

    public function addProduction(int $symbolId, array ...$production): void
    {
        $this->nonTerminalMap[$symbolId] = array_merge(
            $this->nonTerminalMap[$symbolId] ?? [],
            $production
        );
    }

    public function getStartSymbol(): int
    {
        return $this->startSymbol;
    }

    public function getEoiSymbol(): int
    {
        return $this->eoiSymbol;
    }

    /**
     * @param int $symbolId
     * @return bool
     * @throws Exception
     */
    public function isTerminal(int $symbolId): bool
    {
        if (isset($this->terminalMap[$symbolId])) {
            return true;
        };
        if (isset($this->nonTerminalMap[$symbolId])) {
            return false;
        }
        throw new Exception("Symbol {$symbolId} is undefined");
    }

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @return bool
     * @throws Exception
     */
    public function tokenMatchesTerminal(int $symbolId, int $tokenId): bool
    {
        return in_array($tokenId, $this->getTerminalTokenList($symbolId));
    }

    /**
     * @param int $symbolId
     * @return array
     * @throws Exception
     */
    public function getTerminalTokenList(int $symbolId): array
    {
        if (!$this->isTerminal($symbolId)) {
            throw new Exception("Symbol {$symbolId} is not defined as terminal");
        }
        return $this->terminalMap[$symbolId];
    }

    public function getTerminalList(): array
    {
        return array_keys($this->terminalMap);
    }

    public function getNonTerminalList(): array
    {
        return array_keys($this->nonTerminalMap);
    }

    /**
     * @param int $symbolId
     * @return array
     * @throws Exception
     */
    public function getProductionList(int $symbolId): array
    {
        if ($this->isTerminal($symbolId)) {
            throw new Exception("Symbol {$symbolId} is terminal and has no productions");
        }
        return $this->nonTerminalMap[$symbolId];
    }

    /**
     * @return Generator
     * @throws Exception
     */
    public function getFullProductionList(): Generator
    {
        foreach ($this->getNonTerminalList() as $symbolId) {
            foreach ($this->getProductionList($symbolId) as $production) {
                yield [$symbolId, $production];
            }
        }
    }
}
