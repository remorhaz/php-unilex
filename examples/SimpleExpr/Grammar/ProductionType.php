<?php

namespace Remorhaz\UniLex\Example\SimpleExpr\Grammar;

abstract class ProductionType
{
    const T_PLUS            = 0x01; // +
    const T_STAR            = 0x02; // *
    const T_L_PARENTHESIS   = 0x03; // (
    const T_R_PARENTHESIS   = 0x04; // )
    const T_ID              = 0x05; // id
    const T_EOI             = 0x06; // end of input
    const NT_E0             = 0x07; // E
    const NT_E1             = 0x08; // E'
    const NT_T0             = 0x09; // T
    const NT_T1             = 0x0A; // T'
    const NT_F              = 0x0B; // F
}
