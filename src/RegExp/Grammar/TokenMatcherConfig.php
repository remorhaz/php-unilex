<?php

namespace Remorhaz\UniLex\RegExp\Grammar;

use Remorhaz\UniLex\TokenMatcherTemplate;

return [
    'class' => 'Remorhaz\UniLex\RegExp\Grammar\TokenMatcher',
    'template_class' => TokenMatcherTemplate::class,
    'use' => [
        TokenAttribute::class,
        TokenType::class,
    ],
    'before_match' => [
        "unset(\$this->token);",
    ],
    'on_transition' => [
    ],
    'on_token' => [
        "\$this->token = \$tokenFactory->createToken(\$tokenType);",
        "\$this->token->setAttribute(TokenAttribute::CODE, \$char);",
    ],
    'on_error' => [
        "if (\$buffer->isEnd()) {",
        "    return false;",
        "}",
        "\$buffer->nextSymbol();",
        "\$this->token = \$tokenFactory->createToken(TokenType::INVALID);",
        "\$this->token->setAttribute(TokenAttribute::CODE, \$char);",
        "return true;",
    ],
    'token_list' => [
        '[\\u0000-\\u001F]' => [TokenType::CTL_ASCII],
        '[ -#%-\'/:->@_~`]' => [TokenType::PRINTABLE_ASCII_OTHER],
        '\\$' => [TokenType::DOLLAR],
        '\\(' => [TokenType::LEFT_BRACKET],
        '\\)' => [TokenType::RIGHT_BRACKET],
        '\\*' => [TokenType::STAR],
        '\\+' => [TokenType::PLUS],
        ',' => [TokenType::COMMA],
        '-' => [TokenType::HYPHEN],
        '\\.' => [TokenType::DOT],
        '0' => [
            TokenType::DIGIT_ZERO,
            "\$this->token->setAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '[1-7]' => [
            TokenType::DIGIT_OCT,
            "\$this->token->setAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '[8-9]' => [
            TokenType::DIGIT_DEC,
            "\$this->token->setAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '\\?' => [TokenType::QUESTION],
        '[A-Fa-bd-f]' => [
            TokenType::OTHER_HEX_LETTER,
            "\$this->token->setAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '[G-OQ-Zg-nq-tvwyz]' => [TokenType::OTHER_ASCII_LETTER],
        'P' => [TokenType::CAPITAL_P],
        '\\[' => [TokenType::LEFT_SQUARE_BRACKET],
        '\\\\' => [TokenType::BACKSLASH],
        '\\]' => [TokenType::RIGHT_SQUARE_BRACKET],
        '\\^' => [TokenType::CIRCUMFLEX],
        'c' => [
            TokenType::SMALL_C,
            "\$this->token->setAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        'o' => [TokenType::SMALL_O],
        'p' => [TokenType::SMALL_P],
        'u' => [TokenType::SMALL_U],
        'x' => [TokenType::SMALL_X],
        '\\{' => [TokenType::LEFT_CURLY_BRACKET],
        '\\|' => [TokenType::VERTICAL_LINE],
        '}' => [TokenType::RIGHT_CURLY_BRACKET],
        '\\u007F' => [TokenType::OTHER_ASCII],
        '[\\u0080-\\x{10FFFF}]' => [TokenType::NOT_ASCII],
    ],
];
