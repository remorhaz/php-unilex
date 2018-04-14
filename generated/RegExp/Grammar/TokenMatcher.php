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
            $tokenType = 1;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
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
            $tokenType = 29;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x24 == $char) {
            $buffer->nextSymbol();
            $tokenType = 2;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x28 == $char) {
            $buffer->nextSymbol();
            $tokenType = 3;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x29 == $char) {
            $buffer->nextSymbol();
            $tokenType = 4;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2A == $char) {
            $buffer->nextSymbol();
            $tokenType = 5;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2B == $char) {
            $buffer->nextSymbol();
            $tokenType = 6;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2C == $char) {
            $buffer->nextSymbol();
            $tokenType = 7;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2D == $char) {
            $buffer->nextSymbol();
            $tokenType = 8;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2E == $char) {
            $buffer->nextSymbol();
            $tokenType = 9;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x30 == $char) {
            $buffer->nextSymbol();
            $tokenType = 10;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x31 <= $char && $char <= 0x37) {
            $buffer->nextSymbol();
            $tokenType = 11;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x38 == $char || 0x39 == $char) {
            $buffer->nextSymbol();
            $tokenType = 12;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x3F == $char) {
            $buffer->nextSymbol();
            $tokenType = 13;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x41 <= $char && $char <= 0x46 || 0x61 == $char || 0x62 == $char || 0x64 <= $char && $char <= 0x66) {
            $buffer->nextSymbol();
            $tokenType = 27;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
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
            $tokenType = 28;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x50 == $char) {
            $buffer->nextSymbol();
            $tokenType = 14;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5B == $char) {
            $buffer->nextSymbol();
            $tokenType = 15;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5C == $char) {
            $buffer->nextSymbol();
            $tokenType = 16;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5D == $char) {
            $buffer->nextSymbol();
            $tokenType = 17;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5E == $char) {
            $buffer->nextSymbol();
            $tokenType = 18;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x63 == $char) {
            $buffer->nextSymbol();
            $tokenType = 19;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            $this->token->setAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x6F == $char) {
            $buffer->nextSymbol();
            $tokenType = 20;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x70 == $char) {
            $buffer->nextSymbol();
            $tokenType = 21;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x75 == $char) {
            $buffer->nextSymbol();
            $tokenType = 22;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x78 == $char) {
            $buffer->nextSymbol();
            $tokenType = 23;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7B == $char) {
            $buffer->nextSymbol();
            $tokenType = 24;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7C == $char) {
            $buffer->nextSymbol();
            $tokenType = 25;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7D == $char) {
            $buffer->nextSymbol();
            $tokenType = 26;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7F == $char) {
            $buffer->nextSymbol();
            $tokenType = 31;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x80 <= $char && $char <= 0x10FFFF) {
            $buffer->nextSymbol();
            $tokenType = 32;
            $this->token = $tokenFactory->createToken($tokenType);
            $this->token->setAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        goto error;

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
