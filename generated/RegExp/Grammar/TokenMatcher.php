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
        if (0x00 <= $char && $char <= 0x1F) {
            $buffer->nextSymbol();
            goto state2;
        }
        if (0x20 <= $char && $char <= 0x23 ||
            0x25 <= $char && $char <= 0x27 ||
            0x2F == $char ||
            0x3A <= $char && $char <= 0x3E ||
            0x40 == $char ||
            0x5F == $char ||
            0x60 == $char ||
            0x7E == $char
        ) {
            $buffer->nextSymbol();
            goto state3;
        }
        if (0x24 == $char) {
            $buffer->nextSymbol();
            goto state4;
        }
        if (0x28 == $char) {
            $buffer->nextSymbol();
            goto state5;
        }
        if (0x29 == $char) {
            $buffer->nextSymbol();
            goto state6;
        }
        if (0x2A == $char) {
            $buffer->nextSymbol();
            goto state7;
        }
        if (0x2B == $char) {
            $buffer->nextSymbol();
            goto state8;
        }
        if (0x2C == $char) {
            $buffer->nextSymbol();
            goto state9;
        }
        if (0x2D == $char) {
            $buffer->nextSymbol();
            goto state10;
        }
        if (0x2E == $char) {
            $buffer->nextSymbol();
            goto state11;
        }
        if (0x30 == $char) {
            $buffer->nextSymbol();
            goto state12;
        }
        if (0x31 <= $char && $char <= 0x37) {
            $buffer->nextSymbol();
            goto state13;
        }
        if (0x38 == $char || 0x39 == $char) {
            $buffer->nextSymbol();
            goto state14;
        }
        if (0x3F == $char) {
            $buffer->nextSymbol();
            goto state15;
        }
        if (0x41 <= $char && $char <= 0x46 || 0x61 == $char || 0x62 == $char || 0x64 <= $char && $char <= 0x66) {
            $buffer->nextSymbol();
            goto state16;
        }
        if (0x47 <= $char && $char <= 0x4F ||
            0x51 <= $char && $char <= 0x5A ||
            0x67 <= $char && $char <= 0x6E ||
            0x71 <= $char && $char <= 0x74 ||
            0x76 == $char ||
            0x77 == $char ||
            0x79 == $char ||
            0x7A == $char
        ) {
            $buffer->nextSymbol();
            goto state17;
        }
        if (0x50 == $char) {
            $buffer->nextSymbol();
            goto state18;
        }
        if (0x5B == $char) {
            $buffer->nextSymbol();
            goto state19;
        }
        if (0x5C == $char) {
            $buffer->nextSymbol();
            goto state20;
        }
        if (0x5D == $char) {
            $buffer->nextSymbol();
            goto state21;
        }
        if (0x5E == $char) {
            $buffer->nextSymbol();
            goto state22;
        }
        if (0x63 == $char) {
            $buffer->nextSymbol();
            goto state23;
        }
        if (0x6F == $char) {
            $buffer->nextSymbol();
            goto state24;
        }
        if (0x70 == $char) {
            $buffer->nextSymbol();
            goto state25;
        }
        if (0x75 == $char) {
            $buffer->nextSymbol();
            goto state26;
        }
        if (0x78 == $char) {
            $buffer->nextSymbol();
            goto state27;
        }
        if (0x7B == $char) {
            $buffer->nextSymbol();
            goto state28;
        }
        if (0x7C == $char) {
            $buffer->nextSymbol();
            goto state29;
        }
        if (0x7D == $char) {
            $buffer->nextSymbol();
            goto state30;
        }
        if (0x7F == $char) {
            $buffer->nextSymbol();
            goto state31;
        }
        if (0x80 <= $char && $char <= 0x10FFFF) {
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
