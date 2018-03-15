<?php

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

use Remorhaz\UniLex\Exception;

class Table implements TableInterface
{

    private $map = [];

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @param int $productionIndex
     * @throws Exception
     */
    public function addProduction(int $symbolId, int $tokenId, int $productionIndex): void
    {
        if ($this->hasProduction($symbolId, $tokenId)) {
            throw new Exception("Production for [{$symbolId}:{$tokenId}] is already defined");
        }
        $this->map[$symbolId][$tokenId] = $productionIndex;
    }

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @return int
     * @throws Exception
     */
    public function getProductionIndex(int $symbolId, int $tokenId): int
    {
        if (!$this->hasProduction($symbolId, $tokenId)) {
            throw new Exception("Production for [{$symbolId}:{$tokenId}] is not defined");
        }
        return $this->map[$symbolId][$tokenId];
    }

    public function hasProduction(int $symbolId, int $tokenId): bool
    {
        return isset($this->map[$symbolId][$tokenId]);
    }

    public function exportMap(): array
    {
        return $this->map;
    }

    public function importMap(array $map): void
    {
        $this->map = $map;
    }
}
