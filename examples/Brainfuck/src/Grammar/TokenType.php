<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

class TokenType
{

    public const NEXT       = 0x01;
    public const PREV       = 0x02;
    public const INC        = 0x03;
    public const DEC        = 0x04;
    public const OUTPUT     = 0x05;
    public const INPUT      = 0x06;
    public const LOOP       = 0x07;
    public const END_LOOP   = 0x08;

    public const EOI        = 0xFF;
}
