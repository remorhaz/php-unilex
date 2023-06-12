<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

interface SetInterface
{
    /**
     * @param int $symbolId
     * @return list<int>
     */
    public function getTokens(int $symbolId): array;
}
