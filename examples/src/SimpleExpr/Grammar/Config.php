<?php

use Remorhaz\UniLex\Example\SimpleExpr\Grammar\SymbolType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;

return [

    /**
     * Map of terminal symbols and matching tokens.
     * Format:
     *      terminal symbol ID => [token 1 ID, token 2 ID, ...]
     */
    GrammarLoader::TOKEN_MAP_KEY => [
        SymbolType::T_PLUS => TokenType::PLUS, // +
        SymbolType::T_STAR => TokenType::STAR, // *
        SymbolType::T_L_PARENTHESIS => TokenType::L_PARENTHESIS, // (
        SymbolType::T_R_PARENTHESIS => TokenType::R_PARENTHESIS, // )
        SymbolType::T_ID => TokenType::ID, // id
        SymbolType::T_EOI => TokenType::EOI, // end of input
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
            // ROOT → E $
            [SymbolType::NT_E0, SymbolType::T_EOI],
        ],
        SymbolType::NT_E0 => [
            // E  →  T E'
            [SymbolType::NT_T0, SymbolType::NT_E1],
        ],
        SymbolType::NT_E1 => [
            // E' →  + T E'
            [SymbolType::T_PLUS, SymbolType::NT_T0, SymbolType::NT_E1],
            // E' →  ε
            [],
        ],
        SymbolType::NT_T0 => [
            // T  →  F T'
            [SymbolType::NT_F, SymbolType::NT_T1],
        ],
        SymbolType::NT_T1 => [
            // T' →  * F T'
            [SymbolType::T_STAR, SymbolType::NT_F, SymbolType::NT_T1],
            // T' →  ε
            [],
        ],
        SymbolType::NT_F => [
            // F  →  ( E )
            [SymbolType::T_L_PARENTHESIS, SymbolType::NT_E0, SymbolType::T_R_PARENTHESIS],
            // F  →  id
            [SymbolType::T_ID],
        ],
    ],

    /**
     * Virtual root symbol.
     */
    GrammarLoader::ROOT_SYMBOL_KEY => SymbolType::NT_ROOT,

    /**
     * Starting symbol.
     */
    GrammarLoader::START_SYMBOL_KEY => SymbolType::NT_E0,

    /**
     * Symbol that marks end of input.
     */
    GrammarLoader::EOI_SYMBOL_KEY => SymbolType::T_EOI,
];
