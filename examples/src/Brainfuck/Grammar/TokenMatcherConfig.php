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
        ">" => ["\$context->setNewToken(TokenType::NEXT);"],
        "<" => ["\$context->setNewToken(TokenType::PREV);"],
        "\\+" => ["\$context->setNewToken(TokenType::INC);"],
        "-" => ["\$context->setNewToken(TokenType::DEC);"],
        "\\." => ["\$context->setNewToken(TokenType::OUTPUT);"],
        "," => ["\$context->setNewToken(TokenType::INPUT);"],
        "\\[" => ["\$context->setNewToken(TokenType::LOOP);"],
        "]" => ["\$context->setNewToken(TokenType::END_LOOP);"],
    ],
];
