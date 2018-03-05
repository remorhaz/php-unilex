<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

interface TableInterface
{

    public function getProductionIndex(int $symbolId, int $tokenId): int;

    public function hasProduction(int $symbolId, int $tokenId): bool;

    public function exportMap(): array;
}
