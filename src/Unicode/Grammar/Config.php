<?php

use Remorhaz\UniLex\Grammar\ContextFreeGrammarLoader;
use Remorhaz\UniLex\Unicode\Grammar\ProductionType;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

// @todo Lexers probably need more simple and convenient way to access grammar data than CFG.
return [
    ContextFreeGrammarLoader::TOKEN_MAP_KEY => [
        ProductionType::T_DATA => [TokenType::SYMBOL, TokenType::INVALID_BYTES],
        ProductionType::T_EOI => [TokenType::EOI],
    ],
    ContextFreeGrammarLoader::PRODUCTION_MAP_KEY => [
    ],
    ContextFreeGrammarLoader::START_SYMBOL_KEY => 0,
    ContextFreeGrammarLoader::EOI_SYMBOL_KEY => ProductionType::T_EOI,
];
