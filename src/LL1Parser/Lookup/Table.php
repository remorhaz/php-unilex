<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

use Remorhaz\UniLex\Exception;

class Table implements TableInterface
{

    private $map = [];

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @param int[] ...$symbolIdList
     * @throws Exception
     */
    public function addProduction(int $symbolId, int $tokenId, int ...$symbolIdList): void
    {
        if ($this->hasProduction($symbolId, $tokenId)) {
            throw new Exception("Production for [{$symbolId}:{$tokenId}] is already defined");
        }
        $this->map[$symbolId][$tokenId] = $symbolIdList;
    }

    /**
     * @param int $symbolId
     * @param int $tokenId
     * @return array
     * @throws Exception
     */
    public function getProduction(int $symbolId, int $tokenId): array
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
}
