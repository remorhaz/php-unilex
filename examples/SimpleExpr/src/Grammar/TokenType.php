<?php

namespace Remorhaz\UniLex\Example\SimpleExpr\Grammar;

abstract class TokenType
{
    public const PLUS          = 0x01; // +
    public const STAR          = 0x02; // *
    public const L_PARENTHESIS = 0x03; // (
    public const R_PARENTHESIS = 0x04; // )
    public const ID            = 0x05; // id
    public const EOI           = 0x06; // end of input
}
