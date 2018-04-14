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
            goto state2;
        }
        if (0x3C == $char) {
            $buffer->nextSymbol();
            goto state3;
        }
        if (0x2B == $char) {
            $buffer->nextSymbol();
            goto state4;
        }
        if (0x2D == $char) {
            $buffer->nextSymbol();
            goto state5;
        }
        if (0x2E == $char) {
            $buffer->nextSymbol();
            goto state6;
        }
        if (0x2C == $char) {
            $buffer->nextSymbol();
            goto state7;
        }
        if (0x5B == $char) {
            $buffer->nextSymbol();
            goto state8;
        }
        if (0x5D == $char) {
            $buffer->nextSymbol();
            goto state9;
        }
        goto error;

        state2:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state3:
        $tokenType = 2;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state4:
        $tokenType = 3;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state5:
        $tokenType = 4;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state6:
        $tokenType = 5;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state7:
        $tokenType = 6;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state8:
        $tokenType = 7;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        state9:
        $tokenType = 8;
        $this->token = $tokenFactory->createToken($tokenType);
        return true;

        error:
        return false;
    }
}
