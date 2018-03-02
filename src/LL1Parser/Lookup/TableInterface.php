<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

interface TableInterface
{

    public function getProduction(int $symbolId, int $tokenId): array;

    public function hasProduction(int $symbolId, int $tokenId): bool;

    public function exportMap(): array;
}
