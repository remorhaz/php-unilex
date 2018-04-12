<?php

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\TokenMatcherTemplate;

return [
    'class' => 'Remorhaz\UniLex\Example\Brainfuck\TokenMatcher',
    'template_class' => TokenMatcherTemplate::class,
    'use' => [
        TokenType::class,
    ],
    'before_match' => [
        "unset(\$this->token);",
    ],
    'on_token' => [
        "\$this->token = \$tokenFactory->createToken(\$tokenType);",
    ],
    'token_list' => [
        ">" => [TokenType::NEXT],
        "<" => [TokenType::PREV],
        "\\+" => [TokenType::INC],
        "-" => [TokenType::DEC],
        "\\." => [TokenType::OUTPUT],
        "," => [TokenType::INPUT],
        "\\[" => [TokenType::LOOP],
        "]" => [TokenType::ENDLOOP],
    ],
];
