<?php

use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ProductionType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;

return [

    /**
     * Map of terminal symbols and matching tokens.
     * Format:
     *      terminal symbol ID => [token 1 ID, token 2 ID, ...]
     */
    GrammarLoader::TOKEN_MAP_KEY => [
        ProductionType::T_PLUS => [TokenType::PLUS], // +
        ProductionType::T_STAR => [TokenType::STAR], // *
        ProductionType::T_L_PARENTHESIS => [TokenType::L_PARENTHESIS], // (
        ProductionType::T_R_PARENTHESIS => [TokenType::R_PARENTHESIS], // )
        ProductionType::T_ID => [TokenType::ID], // id
        ProductionType::T_EOI => [TokenType::EOI], // end of input
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
        ProductionType::NT_E0 => [
            // E  →  T E'
            [ProductionType::NT_T0, ProductionType::NT_E1],
        ],
        ProductionType::NT_E1 => [
            // E' →  + T E'
            [ProductionType::T_PLUS, ProductionType::NT_T0, ProductionType::NT_E1],
            // E' →  ε
            [],
        ],
        ProductionType::NT_T0 => [
            // T  →  F T'
            [ProductionType::NT_F, ProductionType::NT_T1],
        ],
        ProductionType::NT_T1 => [
            // T' →  * F T'
            [ProductionType::T_STAR, ProductionType::NT_F, ProductionType::NT_T1],
            // T' →  ε
            [],
        ],
        ProductionType::NT_F => [
            // F  →  ( E )
            [ProductionType::T_L_PARENTHESIS, ProductionType::NT_E0, ProductionType::T_R_PARENTHESIS],
            // F  →  id
            [ProductionType::T_ID],
        ],
    ],

    /**
     * Starting symbol.
     */
    GrammarLoader::START_SYMBOL_KEY => ProductionType::NT_E0,

    /**
     * Symbol that marks end of input.
     */
    GrammarLoader::EOI_SYMBOL_KEY => ProductionType::T_EOI,
];
