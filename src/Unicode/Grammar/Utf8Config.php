<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

return [
    'class' => 'Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher',
    'template_class' => Utf8TokenMatcherTemplate::class,
    'use' => [
        TokenAttribute::class,
        TokenType::class,
    ],
    'before_match' => [
        "unset(\$this->token);",
        "\$charList = [];",
    ],
    'on_transition' => [
        "\$charList[] = \$char;",
    ],
    'on_token' => [
        "\$this->token = \$tokenFactory->createToken(\$tokenType);",
    ],
    'on_error' => [
        "\$buffer->nextSymbol();",
        "\$this->token = \$tokenFactory->createToken(TokenType::INVALID_BYTES);",
        "return true;",
    ],
    'token_list' => [
        // 1-byte symbol
        '[\\x00-\\x7F]' => [
            TokenType::SYMBOL,
            "\$this->token->setAttribute(TokenAttribute::UNICODE_CHAR, \$char);",
        ],
        // 2-byte symbol
        '[\\xC0-\\xDF][\\x80-\\xBF]' => [
            TokenType::SYMBOL,
            "\$symbol = (\$charList[0] & 0x1F) << 6;",
            "\$symbol |= (\$charList[1] & 0x3F);",
            "\$this->token->setAttribute(TokenAttribute::UNICODE_CHAR, \$symbol);",
        ],
        // 3-byte symbol
        '[\\xE0-\\xEF][\\x80-\\xBF]{2}' => [
            TokenType::SYMBOL,
            "\$symbol = (\$charList[0] & 0x0F) << 12;",
            "\$symbol |= (\$charList[1] & 0x3F) << 6;",
            "\$symbol |= (\$charList[2] & 0x3F);",
            "\$this->token->setAttribute(TokenAttribute::UNICODE_CHAR, \$symbol);",
        ],
        // 4-byte symbol
        '[\\xE0-\\xEF][\\x80-\\xBF]{3}' => [
            TokenType::SYMBOL,
            "\$symbol = (\$charList[0] & 0x07) << 18;",
            "\$symbol |= (\$charList[1] & 0x3F) << 12;",
            "\$symbol |= (\$charList[2] & 0x3F) << 6;",
            "\$symbol |= (\$charList[3] & 0x3F);",
            "\$this->token->setAttribute(TokenAttribute::UNICODE_CHAR, \$symbol);",
        ],
        // 5-byte symbol
        '[\\xE0-\\xEF][\\x80-\\xBF]{4}' => [
            TokenType::SYMBOL,
            "\$symbol = (\$charList[0] & 0x03) << 24;",
            "\$symbol |= (\$charList[1] & 0x3F) << 18;",
            "\$symbol |= (\$charList[2] & 0x3F) << 12;",
            "\$symbol |= (\$charList[3] & 0x3F) << 6;",
            "\$symbol |= (\$charList[4] & 0x3F);",
            "\$this->token->setAttribute(TokenAttribute::UNICODE_CHAR, \$symbol);",
        ],
        // 6-byte symbol
        '[\\xE0-\\xEF][\\x80-\\xBF]{5}' => [
            TokenType::SYMBOL,
            "\$symbol = (\$charList[0] & 0x01) << 30;",
            "\$symbol |= (\$charList[1] & 0x03) << 24;",
            "\$symbol |= (\$charList[2] & 0x3F) << 18;",
            "\$symbol |= (\$charList[3] & 0x3F) << 12;",
            "\$symbol |= (\$charList[4] & 0x3F) << 6;",
            "\$symbol |= (\$charList[5] & 0x3F);",
            "\$this->token->setAttribute(TokenAttribute::UNICODE_CHAR, \$symbol);",
        ],
    ],
];
