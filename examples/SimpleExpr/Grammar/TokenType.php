<?php

namespace Remorhaz\UniLex\Example\SimpleExpr\Grammar;

abstract class TokenType
{
    const PLUS          = 0x01; // +
    const STAR          = 0x02; // *
    const L_PARENTHESIS = 0x03; // (
    const R_PARENTHESIS = 0x04; // )
    const ID            = 0x05; // id
    const EOI           = 0x06; // end of input
}
