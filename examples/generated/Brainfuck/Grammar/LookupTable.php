<?php
/**
 * Brainfuck LL(1) parser lookup table.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing example-brainfuck-table
 *
 * Phing version: 2.16.1
 */

use Remorhaz\UniLex\Example\Brainfuck\Grammar\SymbolType;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenType;

return [
    SymbolType::NT_EXPRESSION => [
        TokenType::NEXT => 0,
        TokenType::LOOP => 1,
        TokenType::EOI => 2,
        TokenType::END_LOOP => 2,
    ],
    SymbolType::NT_COMMAND => [
        TokenType::NEXT => 0,
    ],
    SymbolType::NT_LOOP => [
        TokenType::LOOP => 0,
    ],
];
