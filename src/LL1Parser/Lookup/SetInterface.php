<?php

namespace Remorhaz\UniLex\LL1Parser\Lookup;

interface SetInterface
{

    public function getTokens(int $symbolId): array;
}
