<?php

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Remorhaz\UniLex\Exception;

class Grammar implements GrammarInterface
{

    private $tokenMap = [];

    private $productionMap =[];

    private $rootSymbol;

    private $startSymbol;

    private $eoiSymbol;

    /**
     * Constructor. Accepts non-empty maps of terminal and non-terminal productions separately.
     *
     * @param int $rootSymbol
     * @param int $startSymbol
     * @param int $eoiSymbol
     */
    public function __construct(int $rootSymbol, int $startSymbol, int $eoiSymbol)
    {
        $this->rootSymbol = $rootSymbol;
        $this->startSymbol = $startSymbol;
        $this->eoiSymbol = $eoiSymbol;
    }

    /**
     * @param int $symbolId
     * @param int $tokenId
     */
    public function addToken(int $symbolId, int $tokenId): void
    {
        $this->tokenMap[$symbolId] = $tokenId;
    }

    public function addProduction(int $headerId, int ...$symbolIdList): void
    {
        $isFirstProduction = !isset($this->productionMap[$headerId]);
        $index = $isFirstProduction
            ? 0
            : count($this->productionMap[$headerId]);
        $production = new Production($headerId, $index, ...$symbolIdList);
        $this->productionMap[$production->getHeaderId()][$production->getIndex()] = $production;
    }

    public function getRootSymbol(): int
    {
        return $this->rootSymbol;
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
        if (isset($this->tokenMap[$symbolId])) {
            return true;
        };
        if (isset($this->productionMap[$symbolId])) {
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
        if (!in_array($tokenId, $this->tokenMap)) {
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
        return $this->tokenMap[$symbolId];
    }

    public function getTerminalList(): array
    {
        return array_keys($this->tokenMap);
    }

    public function getNonTerminalList(): array
    {
        return array_keys($this->productionMap);
    }

    /**
     * @param int $symbolId
     * @return Production[]
     * @throws Exception
     */
    public function getProductionList(int $symbolId): array
    {
        if ($this->isTerminal($symbolId)) {
            throw new Exception("Symbol {$symbolId} is terminal and can't have productions");
        }
        return $this->productionMap[$symbolId];
    }

    /**
     * @param int $symbolId
     * @param int $productionIndex
     * @return Production
     * @throws Exception
     */
    public function getProduction(int $symbolId, int $productionIndex): Production
    {
        if ($symbolId == $this->getRootSymbol()) {
            return new Production($this->getRootSymbol(), 0, $this->getStartSymbol(), $this->getEoiSymbol());
        }
        $productionList = $this->getProductionList($symbolId);
        if (!isset($productionList[$productionIndex])) {
            throw new Exception("Symbol {$symbolId} has no production at index {$productionIndex}");
        }
        return $productionList[$productionIndex];
    }

    /**
     * @return Production[]
     * @throws Exception
     */
    public function getFullProductionList(): array
    {
        $productionList = [];
        foreach ($this->getNonTerminalList() as $symbolId) {
            foreach ($this->getProductionList($symbolId) as $production) {
                $productionList[] = $production;
            }
        }
        return $productionList;
    }
}
