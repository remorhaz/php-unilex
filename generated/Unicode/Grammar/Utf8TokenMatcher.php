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

use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;

class Utf8TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        $context = $this->createContext($buffer, $tokenFactory);
        goto state1;

        state1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x7F) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SYMBOL)
                ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $char);
            return true;
        }
        if (0xC0 <= $char && $char <= 0xDF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state3;
        }
        if (0xE0 <= $char && $char <= 0xEF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state4;
        }
        if (0xF0 <= $char && $char <= 0xF7) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state5;
        }
        if (0xF8 <= $char && $char <= 0xFB) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state6;
        }
        if (0xFC == $char || 0xFD == $char) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state7;
        }
        goto error;

        state3:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            $charList = $context->getStoredSymbolList();
            $symbol = ($charList[0] & 0x1F) << 6;
            $symbol |= ($charList[1] & 0x3F);
            $context
                ->setNewToken(TokenType::SYMBOL)
                ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
            return true;
        }
        goto error;

        state4:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state20;
        }
        goto error;

        state5:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state17;
        }
        goto error;

        state6:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state13;
        }
        goto error;

        state7:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state8;
        }
        goto error;

        state8:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state9;
        }
        goto error;

        state9:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state10;
        }
        goto error;

        state10:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state11;
        }
        goto error;

        state11:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            $charList = $context->getStoredSymbolList();
            $symbol = ($charList[0] & 0x01) << 30;
            $symbol |= ($charList[1] & 0x03) << 24;
            $symbol |= ($charList[2] & 0x3F) << 18;
            $symbol |= ($charList[3] & 0x3F) << 12;
            $symbol |= ($charList[4] & 0x3F) << 6;
            $symbol |= ($charList[5] & 0x3F);
            $context
                ->setNewToken(TokenType::SYMBOL)
                ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
            return true;
        }
        goto error;

        state13:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state14;
        }
        goto error;

        state14:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state15;
        }
        goto error;

        state15:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            $charList = $context->getStoredSymbolList();
            $symbol = ($charList[0] & 0x03) << 24;
            $symbol |= ($charList[1] & 0x3F) << 18;
            $symbol |= ($charList[2] & 0x3F) << 12;
            $symbol |= ($charList[3] & 0x3F) << 6;
            $symbol |= ($charList[4] & 0x3F);
            $context
                ->setNewToken(TokenType::SYMBOL)
                ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
            return true;
        }
        goto error;

        state17:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            goto state18;
        }
        goto error;

        state18:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            $charList = $context->getStoredSymbolList();
            $symbol = ($charList[0] & 0x07) << 18;
            $symbol |= ($charList[1] & 0x3F) << 12;
            $symbol |= ($charList[2] & 0x3F) << 6;
            $symbol |= ($charList[3] & 0x3F);
            $context
                ->setNewToken(TokenType::SYMBOL)
                ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
            return true;
        }
        goto error;

        state20:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x80 <= $char && $char <= 0xBF) {
            $context->storeCurrentSymbol();
            $context->getBuffer()->nextSymbol();
            $charList = $context->getStoredSymbolList();
            $symbol = ($charList[0] & 0x0F) << 12;
            $symbol |= ($charList[1] & 0x3F) << 6;
            $symbol |= ($charList[2] & 0x3F);
            $context
                ->setNewToken(TokenType::SYMBOL)
                ->setTokenAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
            return true;
        }
        goto error;

        error:
        if ($context->getBuffer()->isEnd()) {
            return false;
        }
        $context->getBuffer()->nextSymbol();
        $context->setNewToken(TokenType::INVALID_BYTES);
        return true;
    }
}
