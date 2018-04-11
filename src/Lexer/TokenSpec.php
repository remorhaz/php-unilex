<?php

namespace Remorhaz\UniLex\Lexer;

class TokenSpec
{

    private $regExp;

    private $tokenType;

    private $code;

    public function __construct(string $regExp, int $tokenType, string $code)
    {
        $this->regExp = $regExp;
        $this->tokenType = $tokenType;
        $this->code = $code;
    }

    public function getRegExp(): string
    {
        return $this->regExp;
    }

    public function getTokenType(): int
    {
        return $this->tokenType;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
