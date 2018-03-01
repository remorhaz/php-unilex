<?php

namespace Remorhaz\UniLex\LL1Parser;

interface LookupSetInterface
{

    public function getTokens(int $symbolId): array;
}
