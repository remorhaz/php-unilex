<?php
/**
 * Brainfuck token matcher.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing example-brainfuck-matcher
 *
 * Phing version: 2.16.1
 */

namespace Remorhaz\UniLex\Example\Brainfuck\Grammar;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\TokenFactoryInterface;
use Remorhaz\UniLex\TokenMatcherTemplate;

class TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        unset($this->token);
        goto state1;

        state1:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (0x3E == $char) {
            $buffer->nextSymbol();
            $tokenType = 1;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x3C == $char) {
            $buffer->nextSymbol();
            $tokenType = 2;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x2B == $char) {
            $buffer->nextSymbol();
            $tokenType = 3;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x2D == $char) {
            $buffer->nextSymbol();
            $tokenType = 4;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x2E == $char) {
            $buffer->nextSymbol();
            $tokenType = 5;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x2C == $char) {
            $buffer->nextSymbol();
            $tokenType = 6;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x5B == $char) {
            $buffer->nextSymbol();
            $tokenType = 7;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        if (0x5D == $char) {
            $buffer->nextSymbol();
            $tokenType = 8;
            $this->token = $tokenFactory->createToken($tokenType);
            return true;
        }
        goto error;

        error:
        return false;
    }
}
