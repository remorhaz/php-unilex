<?php

namespace Remorhaz\UniLex\LL1Parser;

interface LookupFirstInterface extends LookupSetInterface
{

    /**
     * Returns FIRST(X1..XN) set.
     *
     * @param int[] ...$symbolIdList
     * @return array
     */
    public function getProductionTokens(int ...$symbolIdList): array;

    /**
     * Reports presence of ε-production in FIRST(X1..XN) sets for all given X.
     *
     * @param int[] ...$symbolIdList
     * @return bool
     */
    public function productionHasEpsilon(int ...$symbolIdList): bool;
}
