<?php
/**
 * Unicode UTF-8 token matcher.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing unicode-utf8-matcher
 *
 * Phing version: 2.16.1
 */

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\TokenFactoryInterface;
use Remorhaz\UniLex\TokenMatcherTemplate;

class Utf8TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        unset($this->token);
        $charList = [];
        goto state1;

        state1:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (0 <= $char && $char <= 127) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state2;
        }
        if (192 <= $char && $char <= 223) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state3;
        }
        if (224 <= $char && $char <= 239) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state4;
        }
        if (240 <= $char && $char <= 247) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state5;
        }
        if (248 <= $char && $char <= 251) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state6;
        }
        if (252 <= $char && $char <= 253) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state7;
        }
        goto error;

        state2:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $char);
        return true;

        state3:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state22;
        }
        goto error;

        state4:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state20;
        }
        goto error;

        state5:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state17;
        }
        goto error;

        state6:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state13;
        }
        goto error;

        state7:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state8;
        }
        goto error;

        state8:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state9;
        }
        goto error;

        state9:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state10;
        }
        goto error;

        state10:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state11;
        }
        goto error;

        state11:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state12;
        }
        goto error;

        state12:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x01) << 30;
        $symbol |= ($charList[1] & 0x03) << 24;
        $symbol |= ($charList[2] & 0x3F) << 18;
        $symbol |= ($charList[3] & 0x3F) << 12;
        $symbol |= ($charList[4] & 0x3F) << 6;
        $symbol |= ($charList[5] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state13:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state14;
        }
        goto error;

        state14:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state15;
        }
        goto error;

        state15:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state16;
        }
        goto error;

        state16:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x03) << 24;
        $symbol |= ($charList[1] & 0x3F) << 18;
        $symbol |= ($charList[2] & 0x3F) << 12;
        $symbol |= ($charList[3] & 0x3F) << 6;
        $symbol |= ($charList[4] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state17:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state18;
        }
        goto error;

        state18:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state19;
        }
        goto error;

        state19:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x07) << 18;
        $symbol |= ($charList[1] & 0x3F) << 12;
        $symbol |= ($charList[2] & 0x3F) << 6;
        $symbol |= ($charList[3] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state20:
        if ($buffer->isEnd()) {
            goto error;
        }
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state21;
        }
        goto error;

        state21:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x0F) << 12;
        $symbol |= ($charList[1] & 0x3F) << 6;
        $symbol |= ($charList[2] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state22:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x1F) << 6;
        $symbol |= ($charList[1] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        error:
        if ($buffer->isEnd()) {
            return false;
        }
        $buffer->nextSymbol();
        $this->token = $tokenFactory->createToken(TokenType::INVALID_BYTES);
        return true;
    }
}
