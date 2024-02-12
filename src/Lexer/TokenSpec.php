<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

class TokenSpec
{
    public function __construct(
        private readonly string $regExp,
        private readonly string $code,
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
