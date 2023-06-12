<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

interface TableInterface
{
    public function getProductionIndex(int $symbolId, int $tokenId): int;

    /**
     * @param int $symbolId
     * @return array<int, int>
     */
    public function getExpectedTokenList(int $symbolId): array;

    public function hasProduction(int $symbolId, int $tokenId): bool;

    /**
     * @return array<int, array<int, int>>
     */
    public function exportMap(): array;
}
