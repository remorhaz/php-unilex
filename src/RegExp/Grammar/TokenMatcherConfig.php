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
    'on_error' => [
        "if (\$context->getBuffer()->isEnd()) {",
        "    return false;",
        "}",
        "\$char = \$context->getBuffer()->getSymbol();",
        "\$context->getBuffer()->nextSymbol();",
        "\$context",
        "   ->setNewToken(TokenType::INVALID)",
        "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        "return true;",
    ],
    'token_list' => [
        '[\\u0000-\\u001F]' => [
            "\$context",
            "   ->setNewToken(TokenType::CTL_ASCII)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '[ -#%-\'/:->@_~`]' => [
            "\$context",
            "   ->setNewToken(TokenType::PRINTABLE_ASCII_OTHER)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\$' => [
            "\$context",
            "   ->setNewToken(TokenType::DOLLAR)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\(' => [
            "\$context",
            "   ->setNewToken(TokenType::LEFT_BRACKET)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\)' => [
            "\$context",
            "   ->setNewToken(TokenType::RIGHT_BRACKET)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\*' => [
            "\$context",
            "   ->setNewToken(TokenType::STAR)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\+' => [
            "\$context",
            "   ->setNewToken(TokenType::PLUS)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        ',' => [
            "\$context",
            "   ->setNewToken(TokenType::COMMA)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '-' => [
            "\$context",
            "   ->setNewToken(TokenType::HYPHEN)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\.' => [
            "\$context",
            "   ->setNewToken(TokenType::DOT)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '0' => [
            "\$context",
            "   ->setNewToken(TokenType::DIGIT_ZERO)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char)",
            "   ->setTokenAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '[1-7]' => [
            "\$context",
            "   ->setNewToken(TokenType::DIGIT_OCT)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char)",
            "   ->setTokenAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '[8-9]' => [
            "\$context",
            "   ->setNewToken(TokenType::DIGIT_DEC)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char)",
            "   ->setTokenAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '\\?' => [
            "\$context",
            "   ->setNewToken(TokenType::QUESTION)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '[A-Fa-bd-f]' => [
            "\$context",
            "   ->setNewToken(TokenType::OTHER_HEX_LETTER)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char)",
            "   ->setTokenAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        '[G-OQ-Zg-nq-tvwyz]' => [
            "\$context",
            "   ->setNewToken(TokenType::OTHER_ASCII_LETTER)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        'P' => [
            "\$context",
            "   ->setNewToken(TokenType::CAPITAL_P)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\[' => [
            "\$context",
            "   ->setNewToken(TokenType::LEFT_SQUARE_BRACKET)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\\\' => [
            "\$context",
            "   ->setNewToken(TokenType::BACKSLASH)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\]' => [
            "\$context",
            "   ->setNewToken(TokenType::RIGHT_SQUARE_BRACKET)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\^' => [
            "\$context",
            "   ->setNewToken(TokenType::CIRCUMFLEX)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        'c' => [
            "\$context",
            "   ->setNewToken(TokenType::SMALL_C)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char)",
            "   ->setTokenAttribute(TokenAttribute::DIGIT, chr(\$char));",
        ],
        'o' => [
            "\$context",
            "   ->setNewToken(TokenType::SMALL_O)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        'p' => [
            "\$context",
            "   ->setNewToken(TokenType::SMALL_P)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        'u' => [
            "\$context",
            "   ->setNewToken(TokenType::SMALL_U)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        'x' => [
            "\$context",
            "   ->setNewToken(TokenType::SMALL_X)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\{' => [
            "\$context",
            "   ->setNewToken(TokenType::LEFT_CURLY_BRACKET)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\|' => [
            "\$context",
            "   ->setNewToken(TokenType::VERTICAL_LINE)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '}' => [
            "\$context",
            "   ->setNewToken(TokenType::RIGHT_CURLY_BRACKET)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '\\u007F' => [
            "\$context",
            "   ->setNewToken(TokenType::OTHER_ASCII)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
        '[\\u0080-\\x{10FFFF}]' => [
            "\$context",
            "   ->setNewToken(TokenType::NOT_ASCII)",
            "   ->setTokenAttribute(TokenAttribute::CODE, \$char);",
        ],
    ],
];
