<?php
/**
 * RegExp token matcher.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing regexp-matcher
 *
 * Phing version: 2.16.1
 */

namespace Remorhaz\UniLex\RegExp\Grammar;

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
        if (0 <= $char && $char <= 31) {
            $buffer->nextSymbol();
            goto state2;
        }
        if (32 <= $char && $char <= 35 ||
            37 <= $char && $char <= 39 ||
            $char == 47 ||
            58 <= $char && $char <= 62 ||
            $char == 64 ||
            95 <= $char && $char <= 96 ||
            $char == 126
        ) {
            $buffer->nextSymbol();
            goto state3;
        }
        if ($char == 36) {
            $buffer->nextSymbol();
            goto state4;
        }
        if ($char == 40) {
            $buffer->nextSymbol();
            goto state5;
        }
        if ($char == 41) {
            $buffer->nextSymbol();
            goto state6;
        }
        if ($char == 42) {
            $buffer->nextSymbol();
            goto state7;
        }
        if ($char == 43) {
            $buffer->nextSymbol();
            goto state8;
        }
        if ($char == 44) {
            $buffer->nextSymbol();
            goto state9;
        }
        if ($char == 45) {
            $buffer->nextSymbol();
            goto state10;
        }
        if ($char == 46) {
            $buffer->nextSymbol();
            goto state11;
        }
        if ($char == 48) {
            $buffer->nextSymbol();
            goto state12;
        }
        if (49 <= $char && $char <= 55) {
            $buffer->nextSymbol();
            goto state13;
        }
        if (56 <= $char && $char <= 57) {
            $buffer->nextSymbol();
            goto state14;
        }
        if ($char == 63) {
            $buffer->nextSymbol();
            goto state15;
        }
        if (65 <= $char && $char <= 70 || 97 <= $char && $char <= 98 || 100 <= $char && $char <= 102) {
            $buffer->nextSymbol();
            goto state16;
        }
        if (71 <= $char && $char <= 79 ||
            81 <= $char && $char <= 90 ||
            103 <= $char && $char <= 110 ||
            113 <= $char && $char <= 116 ||
            118 <= $char && $char <= 119 ||
            121 <= $char && $char <= 122
        ) {
            $buffer->nextSymbol();
            goto state17;
        }
        if ($char == 80) {
            $buffer->nextSymbol();
            goto state18;
        }
        if ($char == 91) {
            $buffer->nextSymbol();
            goto state19;
        }
        if ($char == 92) {
            $buffer->nextSymbol();
            goto state20;
        }
        if ($char == 93) {
            $buffer->nextSymbol();
            goto state21;
        }
        if ($char == 94) {
            $buffer->nextSymbol();
            goto state22;
        }
        if ($char == 99) {
            $buffer->nextSymbol();
            goto state23;
        }
        if ($char == 111) {
            $buffer->nextSymbol();
            goto state24;
        }
        if ($char == 112) {
            $buffer->nextSymbol();
            goto state25;
        }
        if ($char == 117) {
            $buffer->nextSymbol();
            goto state26;
        }
        if ($char == 120) {
            $buffer->nextSymbol();
            goto state27;
        }
        if ($char == 123) {
            $buffer->nextSymbol();
            goto state28;
        }
        if ($char == 124) {
            $buffer->nextSymbol();
            goto state29;
        }
        if ($char == 125) {
            $buffer->nextSymbol();
            goto state30;
        }
        if ($char == 127) {
            $buffer->nextSymbol();
            goto state31;
        }
        if (128 <= $char && $char <= 1114111) {
            $buffer->nextSymbol();
            goto state32;
        }
        goto error;

        state2:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state3:
        $tokenType = 29;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state4:
        $tokenType = 2;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state5:
        $tokenType = 3;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state6:
        $tokenType = 4;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state7:
        $tokenType = 5;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state8:
        $tokenType = 6;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state9:
        $tokenType = 7;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state10:
        $tokenType = 8;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state11:
        $tokenType = 9;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state12:
        $tokenType = 10;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
        return true;

        state13:
        $tokenType = 11;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
        return true;

        state14:
        $tokenType = 12;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
        return true;

        state15:
        $tokenType = 13;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state16:
        $tokenType = 27;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
        return true;

        state17:
        $tokenType = 28;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state18:
        $tokenType = 14;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state19:
        $tokenType = 15;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state20:
        $tokenType = 16;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state21:
        $tokenType = 17;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state22:
        $tokenType = 18;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state23:
        $tokenType = 19;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
        return true;

        state24:
        $tokenType = 20;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state25:
        $tokenType = 21;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state26:
        $tokenType = 22;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state27:
        $tokenType = 23;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state28:
        $tokenType = 24;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state29:
        $tokenType = 25;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state30:
        $tokenType = 26;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state31:
        $tokenType = 31;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        state32:
        $tokenType = 32;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;

        error:
        if ($buffer->isEnd()) {
            return false;
        }
        $buffer->nextSymbol();
        $this->token = $tokenFactory->createToken(TokenType::INVALID);
        $this->token->setAttribute(TokenAttribute::CODE, $char);
        return true;
    }
}
