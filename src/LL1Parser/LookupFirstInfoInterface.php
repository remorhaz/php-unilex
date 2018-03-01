<?php

namespace Remorhaz\UniLex\LL1Parser;

interface LookupFirstInfoInterface
{

    /**
     * Returns FIRST(X) set.
     *
     * @param int[] ...$nonTerminalIdList
     * @return array
     */
    public function get(int ...$nonTerminalIdList): array;

    /**
     * Reports presence of ε-production in FIRST(X) sets for all given X.
     *
     * @param int[] ...$nonTerminalIdList
     * @return bool
     */
    public function hasEpsilon(int ...$nonTerminalIdList): bool;
}
