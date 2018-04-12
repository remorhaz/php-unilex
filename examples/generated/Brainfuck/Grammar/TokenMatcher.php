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

namespace Remorhaz\UniLex\Example\Brainfuck;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Example\Brainfuck\Grammar\TokenType;
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
        if ($char == 62) {
            $buffer->nextSymbol();
            goto state2;
        }
        if ($char == 60) {
            $buffer->nextSymbol();
            goto state3;
        }
        if ($char == 43) {
            $buffer->nextSymbol();
            goto state4;
        }
        if ($char == 45) {
            $buffer->nextSymbol();
            goto state5;
        }
        if ($char == 46) {
            $buffer->nextSymbol();
            goto state6;
        }
        if ($char == 44) {
            $buffer->nextSymbol();
            goto state7;
        }
        if ($char == 91) {
            $buffer->nextSymbol();
            goto state8;
        }
        if ($char == 93) {
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
