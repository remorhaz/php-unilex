<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Lexer;

class TokenSpec
{
    private $regExp;

    private $code;

    public function __construct(string $regExp, string $code)
    {
        $this->regExp = $regExp;
        $this->code = $code;
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
