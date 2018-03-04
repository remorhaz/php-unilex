<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\LexemeMatcherInfoInterface;

class SymbolInfo implements LexemeMatcherInfoInterface
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
