<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

abstract class SymbolType
{

    public const NT_ROOT    = 0x00;

    public const T_NEXT     = 0x01;
    public const T_PREV     = 0x02;
    public const T_INC      = 0x03;
    public const T_DEC      = 0x04;
    public const T_OUTPUT   = 0x05;
    public const T_INPUT    = 0x06;
    public const T_LOOP     = 0x07;
    public const T_END_LOOP = 0x08;

    public const NT_EXPRESSION = 0x09;
    public const NT_COMMAND = 0x0A;

    public const T_EOI      = 0xFF;
}
