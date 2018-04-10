<?php

namespace Remorhaz\UniLex\Unicode\Grammar;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\TokenFactoryInterface;

class Utf8TokenMatcher extends Utf8TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        unset($this->token);
        $charList = [];
        goto state1;

        state1:
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
        goto error;

        state2:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $char);
        return true;

        state3:
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state10;
        }
        goto error;

        state4:
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state5;
        }
        goto error;

        state5:
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state6;
        }
        goto error;

        state6:
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state7;
        }
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x0F) << 12;
        $symbol |= ($charList[1] & 0x3F) << 6;
        $symbol |= ($charList[2] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state7:
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state8;
        }
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x07) << 18;
        $symbol |= ($charList[1] & 0x3F) << 12;
        $symbol |= ($charList[2] & 0x3F) << 6;
        $symbol |= ($charList[3] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state8:
        $char = $buffer->getSymbol();
        if (128 <= $char && $char <= 191) {
            $charList[] = $char;
            $buffer->nextSymbol();
            goto state9;
        }
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x03) << 24;
        $symbol |= ($charList[1] & 0x3F) << 18;
        $symbol |= ($charList[2] & 0x3F) << 12;
        $symbol |= ($charList[3] & 0x3F) << 6;
        $symbol |= ($charList[4] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        state9:
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

        state10:
        $tokenType = 1;
        $this->token = $tokenFactory->createToken($tokenType);
        $symbol = ($charList[0] & 0x1F) << 6;
        $symbol |= ($charList[1] & 0x3F);
        $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
        return true;

        error:
        $buffer->nextSymbol();
        $this->token = $tokenFactory->createToken(TokenType::INVALID_BYTES);
        return true;
    }
}
