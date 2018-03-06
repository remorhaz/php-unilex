<?php

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Generator;
use Remorhaz\UniLex\Exception;

class Grammar implements GrammarInterface
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
     * @param int $tokenId
     */
    public function addToken(int $symbolId, int $tokenId): void
    {
        $this->terminalMap[$symbolId] = $tokenId;
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
     * @return int
     * @throws Exception
     */
    public function getEoiToken(): int
    {
        return $this->getToken($this->getEoiSymbol());
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
        throw new Exception("Symbol {$symbolId} is not defined");
    }

    /**
     * @param int $tokenId
     * @return bool
     * @throws Exception
     */
    public function isEoiToken(int $tokenId): bool
    {
        return $this->tokenMatchesTerminal($this->getEoiSymbol(), $tokenId);
    }

    /**
     * @param int $symbolId
     * @return bool
     * @throws Exception
     */
    public function isEoiSymbol(int $symbolId): bool
    {
        return $this->isTerminal($symbolId)
            ? $this->getEoiSymbol() == $symbolId
            : false;
    }

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @return bool
     * @throws Exception
     */
    public function tokenMatchesTerminal(int $symbolId, int $tokenId): bool
    {
        if (!in_array($tokenId, $this->terminalMap)) {
            throw new Exception("Token {$tokenId} is not defined");
        }
        return $this->getToken($symbolId) == $tokenId;
    }

    /**
     * @param int $symbolId
     * @return int
     * @throws Exception
     */
    public function getToken(int $symbolId): int
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
            throw new Exception("Symbol {$symbolId} is terminal and can't have productions");
        }
        return $this->nonTerminalMap[$symbolId];
    }

    /**
     * @param int $symbolId
     * @param int $productionIndex
     * @return array
     * @throws Exception
     */
    public function getProduction(int $symbolId, int $productionIndex): array
    {
        $productionList = $this->getProductionList($symbolId);
        if (!isset($productionList[$productionIndex])) {
            throw new Exception("Symbol {$symbolId} has no production at index {$productionIndex}");
        }
        return $productionList[$productionIndex];
    }

    /**
     * @return iterable
     * @throws Exception
     */
    public function getFullProductionList(): iterable
    {
        foreach ($this->getNonTerminalList() as $symbolId) {
            foreach ($this->getProductionList($symbolId) as $productionIndex => $production) {
                yield [$symbolId, $productionIndex, $production];
            }
        }
    }
}
