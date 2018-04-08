<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\TokenFactoryInterface;

return [
    // 1-byte symbol
    '[\\x00-\\x7F]' => [
        TokenType::SYMBOL,
        function (int $char, TokenFactoryInterface $tokenFactory): void {
            $this->token = $tokenFactory->createToken(TokenType::SYMBOL);
            $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $char);
        }
    ],
];
