<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Stack;

interface PushInterface
{
    public function push(StackableSymbolInterface ...$symbolList): void;
}
