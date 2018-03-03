<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

abstract class TokenType
{
    const SYMBOL        = 0x01;
    const INVALID_BYTES = 0x02;
    const EOI           = 0xFF;
}
