<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\TokenMatcherInfoInterface;

class SymbolInfo implements TokenMatcherInfoInterface
{

    private $code;

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
