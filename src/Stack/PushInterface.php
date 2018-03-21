<?php

namespace Remorhaz\UniLex\Stack;

interface PushInterface
{

    public function push(StackableSymbolInterface ...$symbolList): void;
}
