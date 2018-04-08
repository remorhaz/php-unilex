<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenFactoryInterface;
use Remorhaz\UniLex\TokenMatcherInterface;
use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

class Utf8TokenMatcher implements TokenMatcherInterface
{

    private $token;

    /**
     * @return Token
     * @throws \Remorhaz\UniLex\Exception
     */
    public function getToken(): Token
    {
        if (!isset($this->token)) {
            throw new Exception("Token is not defined");
        }
        return $this->token;
    }

    /**
     * @param CharBufferInterface $buffer
     * @param TokenFactoryInterface $tokenFactory
     * @return bool
     * @throws \Remorhaz\UniLex\Exception
     */
    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        unset($this->token);
        $symbol = null;
        $firstByte = $buffer->getSymbol();
        if ($firstByte >= 0 && $firstByte <= 0x7F) { // 1-byte symbol
            $buffer->nextSymbol();
            $this->token = $tokenFactory->createToken(TokenType::SYMBOL);
            $this->token->setAttribute(TokenAttribute::UNICODE_CHAR, $firstByte);
            return true;
        }
        if ($firstByte >= 0xC0 && $firstByte <= 0xDF) { // 2-byte symbol
            $symbol = ($firstByte & 0x1F) << 6;
            $buffer->nextSymbol();
            goto tail_byte_1;
        }
        if ($firstByte >= 0xE0 && $firstByte <= 0xEF) { // 3-byte symbol
            $symbol = ($firstByte & 0x0F) << 12;
            $buffer->nextSymbol();
            goto tail_byte_2;
        }
        if ($firstByte >= 0xF0 && $firstByte <= 0xF7) { // 4-byte symbol
            $symbol = ($firstByte & 0x07) << 18;
            $buffer->nextSymbol();
            goto tail_byte_3;
        }
        if ($firstByte >= 0xF8 && $firstByte <= 0xFB) { // 5-byte symbol
            $symbol = ($firstByte & 0x03) << 24;
            $buffer->nextSymbol();
            goto tail_byte_4;
        }
        if ($firstByte >= 0xFC && $firstByte <= 0xFD) { // 6-byte symbol
            $symbol = ($firstByte & 0x01) << 30;
            $buffer->nextSymbol();
            goto tail_byte_5;
        }
        goto invalid_byte;

        tail_byte_5:
        $tailByte = $buffer->getSymbol();
        if ($tailByte >= 0x80 && $tailByte <= 0xBF) {
            $symbol |= ($tailByte & 0x3F) << 24;
            $buffer->nextSymbol();
            goto tail_byte_4;
        }
        goto invalid_byte;

        tail_byte_4:
        $tailByte = $buffer->getSymbol();
        if ($tailByte >= 0x80 && $tailByte <= 0xBF) {
            $symbol |= ($tailByte & 0x3F) << 18;
            $buffer->nextSymbol();
            goto tail_byte_3;
        }
        goto invalid_byte;

        tail_byte_3:
        $tailByte = $buffer->getSymbol();
        if ($tailByte >= 0x80 && $tailByte <= 0xBF) {
            $symbol |= ($tailByte & 0x3F) << 12;
            $buffer->nextSymbol();
            goto tail_byte_2;
        }
        goto invalid_byte;

        tail_byte_2:
        $tailByte = $buffer->getSymbol();
        if ($tailByte >= 0x80 && $tailByte <= 0xBF) {
            $symbol |= ($tailByte & 0x3F) << 6;
            $buffer->nextSymbol();
            goto tail_byte_1;
        }
        goto invalid_byte;

        tail_byte_1:
        $tailByte = $buffer->getSymbol();
        if ($tailByte >= 0x80 && $tailByte <= 0xBF) {
            $symbol |= ($tailByte & 0x3F);
            $buffer->nextSymbol();
            $token = $tokenFactory->createToken(TokenType::SYMBOL);
            $token->setAttribute(TokenAttribute::UNICODE_CHAR, $symbol);
            $this->token = $token;
            return true;
        }
        goto invalid_byte;

        invalid_byte:
        $buffer->nextSymbol();
        $this->token = $tokenFactory->createToken(TokenType::INVALID_BYTES);
        return true;
    }
}
