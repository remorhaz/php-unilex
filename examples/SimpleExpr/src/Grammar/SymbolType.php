<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\SimpleExpr\Grammar;

abstract class SymbolType
{
    public const NT_ROOT          = 0x00; // Root symbol fot LL(1) parser

    public const T_PLUS            = 0x01; // +
    public const T_STAR            = 0x02; // *
    public const T_L_PARENTHESIS   = 0x03; // (
    public const T_R_PARENTHESIS   = 0x04; // )
    public const T_ID              = 0x05; // id
    public const T_EOI             = 0x06; // end of input
    public const NT_E0             = 0x07; // E
    public const NT_E1             = 0x08; // E'
    public const NT_T0             = 0x09; // T
    public const NT_T1             = 0x0A; // T'
    public const NT_F              = 0x0B; // F
}
