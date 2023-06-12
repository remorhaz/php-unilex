<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Grammar\ContextFree;

use Remorhaz\UniLex\Exception;

class Grammar implements GrammarInterface
{
    /**
     * @var array<int, int>
     */
    private array $tokenMap = [];

    /**
     * @var array<int, array<int, Production>>
     */
    private array $productionMap = [];

    /**
     * Constructor. Accepts non-empty maps of terminal and non-terminal productions separately.
     */
    public function __construct(
        private int $rootSymbol,
        private int $startSymbol,
        private int $eoiSymbol,
    ) {
    }

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
     * @throws Exception
     */
    public function getEoiToken(): int
    {
        return $this->getToken($this->getEoiSymbol());
    }

    /**
     * @throws Exception
     */
    public function isTerminal(int $symbolId): bool
    {
        if (isset($this->tokenMap[$symbolId])) {
            return true;
        }

        if (isset($this->productionMap[$symbolId])) {
            return false;
        }

        throw new Exception("Symbol {$symbolId} is not defined");
    }

    /**
     * @throws Exception
     */
    public function isEoiToken(int $tokenId): bool
    {
        return $this->tokenMatchesTerminal($this->getEoiSymbol(), $tokenId);
    }

    /**
     * @throws Exception
     */
    public function isEoiSymbol(int $symbolId): bool
    {
        return $this->isTerminal($symbolId) && $this->getEoiSymbol() == $symbolId;
    }

    /**
     * @throws Exception
     */
    public function tokenMatchesTerminal(int $symbolId, int $tokenId): bool
    {
        return in_array($tokenId, $this->tokenMap)
            ? $this->getToken($symbolId) == $tokenId
            : throw new Exception("Token $tokenId is not defined");
    }

    /**
     * @throws Exception
     */
    public function getToken(int $symbolId): int
    {
        return $this->isTerminal($symbolId)
            ? $this->tokenMap[$symbolId]
            : throw new Exception("Symbol {$symbolId} is not defined as terminal");
    }

    /**
     * @return list<int>
     */
    public function getTerminalList(): array
    {
        return array_keys($this->tokenMap);
    }

    /**
     * @return list<int>
     */
    public function getNonTerminalList(): array
    {
        return array_keys($this->productionMap);
    }

    /**
     * @param int $symbolId
     * @return array<int, Production>
     * @throws Exception
     */
    public function getProductionList(int $symbolId): array
    {
        return $this->isTerminal($symbolId)
            ? throw new Exception("Symbol $symbolId is terminal and can't have productions")
            : $this->productionMap[$symbolId];
    }

    /**
     * @throws Exception
     */
    public function getProduction(int $symbolId, int $productionIndex): Production
    {
        if ($symbolId == $this->getRootSymbol()) {
            return new Production($this->getRootSymbol(), 0, $this->getStartSymbol(), $this->getEoiSymbol());
        }

        $productionList = $this->getProductionList($symbolId);

        return $productionList[$productionIndex]
            ?? throw new Exception("Symbol $symbolId has no production at index $productionIndex");
    }

    /**
     * @return list<Production>
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
