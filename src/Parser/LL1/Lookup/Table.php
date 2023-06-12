<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Exception;

class Table implements TableInterface
{
    /**
     * @var array<int, array<int, int>>
     */
    private array $map = [];

    /**
     * @throws Exception
     */
    public function addProduction(int $symbolId, int $tokenId, int $productionIndex): void
    {
        $this->map[$symbolId][$tokenId] = $this->hasProduction($symbolId, $tokenId)
            ? throw new Exception("Production for [{$symbolId}:{$tokenId}] is already defined")
            : $productionIndex;
    }

    /**
     * @throws Exception
     */
    public function getProductionIndex(int $symbolId, int $tokenId): int
    {
        return $this->map[$symbolId][$tokenId]
            ?? throw new Exception("Production for [{$symbolId}:{$tokenId}] is not defined");
    }

    /**
     * @param int $symbolId
     * @return array<int, int>
     */
    public function getExpectedTokenList(int $symbolId): array
    {
        return isset($this->map[$symbolId]) ? array_keys($this->map[$symbolId]) : [];
    }

    public function hasProduction(int $symbolId, int $tokenId): bool
    {
        return isset($this->map[$symbolId][$tokenId]);
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function exportMap(): array
    {
        return $this->map;
    }

    /**
     * @param array<int, array<int, int>> $map
     * @return void
     */
    public function importMap(array $map): void
    {
        $this->map = $map;
    }
}
