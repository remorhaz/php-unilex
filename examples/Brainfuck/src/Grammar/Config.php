<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;

return [
    /**
     * Map of terminal symbols and matching tokens.
     * Format:
     *      terminal symbol ID => [token 1 ID, token 2 ID, ...]
     */
    GrammarLoader::TOKEN_MAP_KEY => [
        SymbolType::T_NEXT => TokenType::NEXT,
        SymbolType::T_PREV => TokenType::PREV,
        SymbolType::T_INC => TokenType::INC,
        SymbolType::T_DEC => TokenType::DEC,
        SymbolType::T_OUTPUT => TokenType::OUTPUT,
        SymbolType::T_INPUT => TokenType::INPUT,
        SymbolType::T_LOOP => TokenType::LOOP,
        SymbolType::T_END_LOOP => TokenType::END_LOOP,
        SymbolType::T_EOI => TokenType::EOI,
    ],

    /**
     * Map of non-terminal symbols and derived productions.
     * Format:
     *      non-terminal symbol ID => [
     *          [Symbol 1 ID, Symbol 2 ID, ...],
     *          [], // empty array defines ε-production
     *      ]
     */
    GrammarLoader::PRODUCTION_MAP_KEY => [
        SymbolType::NT_ROOT => [
            [SymbolType::NT_EXPRESSION, SymbolType::T_EOI],
        ],
        SymbolType::NT_EXPRESSION => [
            [SymbolType::NT_COMMAND, SymbolType::NT_EXPRESSION],
            [SymbolType::NT_LOOP, SymbolType::NT_EXPRESSION],
            [],
        ],
        SymbolType::NT_COMMAND => [
            [SymbolType::T_NEXT],
            [SymbolType::T_PREV],
            [SymbolType::T_INC],
            [SymbolType::T_DEC],
            [SymbolType::T_OUTPUT],
            [SymbolType::T_INPUT],
        ],
        SymbolType::NT_LOOP => [
            [SymbolType::T_LOOP, SymbolType::NT_EXPRESSION, SymbolType::T_END_LOOP],
        ],
    ],

    /**
     * Virtual root symbol.
     */
    GrammarLoader::ROOT_SYMBOL_KEY => SymbolType::NT_ROOT,

    /**
     * Starting symbol.
     */
    GrammarLoader::START_SYMBOL_KEY => SymbolType::NT_EXPRESSION,

    /**
     * Symbol that marks end of input.
     */
    GrammarLoader::EOI_SYMBOL_KEY => SymbolType::T_EOI,
];
