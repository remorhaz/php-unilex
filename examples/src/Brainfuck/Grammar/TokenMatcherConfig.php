<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\TokenMatcherTemplate;

return [
    'class' => 'Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenMatcher',
    'template_class' => TokenMatcherTemplate::class,
    'use' => [
        TokenType::class,
    ],
    'token_list' => [
        ">" => [
            TokenType::NEXT,
            "\$context->setNewToken(TokenType::NEXT);",
        ],
        "<" => [
            TokenType::PREV,
            "\$context->setNewToken(TokenType::PREV);",
        ],
        "\\+" => [
            TokenType::INC,
            "\$context->setNewToken(TokenType::INC);",
        ],
        "-" => [
            TokenType::DEC,
            "\$context->setNewToken(TokenType::DEC);",
        ],
        "\\." => [
            TokenType::OUTPUT,
            "\$context->setNewToken(TokenType::OUTPUT);",
        ],
        "," => [
            TokenType::INPUT,
            "\$context->setNewToken(TokenType::INPUT);",
        ],
        "\\[" => [
            TokenType::LOOP,
            "\$context->setNewToken(TokenType::LOOP);",
        ],
        "]" => [
            TokenType::END_LOOP,
            "\$context->setNewToken(TokenType::END_LOOP);",
        ],
    ],
];
