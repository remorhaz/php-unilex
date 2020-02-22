<?php

/**
 * Brainfuck LL(1) parser lookup table.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing example-brainfuck-table
 *
 * Phing version: 2.16.3
 */

use Remorhaz\UniLex\Example\Brainfuck\Grammar\SymbolType;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenType;

return [
    SymbolType::NT_EXPRESSION => [
        TokenType::NEXT => 0,
        TokenType::PREV => 0,
        TokenType::INC => 0,
        TokenType::DEC => 0,
        TokenType::OUTPUT => 0,
        TokenType::INPUT => 0,
        TokenType::LOOP => 1,
        TokenType::EOI => 2,
        TokenType::END_LOOP => 2,
    ],
    SymbolType::NT_COMMAND => [
        TokenType::NEXT => 0,
        TokenType::PREV => 1,
        TokenType::INC => 2,
        TokenType::DEC => 3,
        TokenType::OUTPUT => 4,
        TokenType::INPUT => 5,
    ],
    SymbolType::NT_LOOP => [
        TokenType::LOOP => 0,
    ],
];
