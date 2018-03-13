<?php

namespace Remorhaz\UniLex\Parser\LL1\Lookup;

interface SetInterface
{

    public function getTokens(int $symbolId): array;
}
