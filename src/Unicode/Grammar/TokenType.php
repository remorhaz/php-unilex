<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Unicode\Grammar;

abstract class TokenType
{
    public const SYMBOL        = 0x01;
    public const INVALID_BYTES = 0x02;
    public const EOI           = 0xFF;
}
