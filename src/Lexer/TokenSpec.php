<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

class TokenSpec
{
    public function __construct(
        private string $regExp,
        private string $code,
    ) {
    }

    public function getRegExp(): string
    {
        return $this->regExp;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
