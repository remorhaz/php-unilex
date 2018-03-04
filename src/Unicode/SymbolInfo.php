<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\MatcherInfoInterface;

class SymbolInfo implements MatcherInfoInterface
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
