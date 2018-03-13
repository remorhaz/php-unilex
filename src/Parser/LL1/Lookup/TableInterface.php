<?php

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

interface TableInterface
{

    public function getProductionIndex(int $symbolId, int $tokenId): int;

    public function hasProduction(int $symbolId, int $tokenId): bool;

    public function exportMap(): array;
}
