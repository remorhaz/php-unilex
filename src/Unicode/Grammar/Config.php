<?php

use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\Unicode\Grammar\ProductionType;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

// @todo Lexers probably need more simple and convenient way to access grammar data than CFG.
return [
    GrammarLoader::TOKEN_MAP_KEY => [
        ProductionType::T_DATA => [TokenType::SYMBOL, TokenType::INVALID_BYTES],
        ProductionType::T_EOI => [TokenType::EOI],
    ],
    GrammarLoader::PRODUCTION_MAP_KEY => [
    ],
    GrammarLoader::START_SYMBOL_KEY => 0,
    GrammarLoader::EOI_SYMBOL_KEY => ProductionType::T_EOI,
];
