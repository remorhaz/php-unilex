<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Exception;
use Remorhaz\UniLex\RegExp\Grammar\TokenAttribute;
use Remorhaz\UniLex\RegExp\Grammar\TokenType;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\TokenFactoryInterface;
use Remorhaz\UniLex\TokenMatcherInterface;

class TokenMatcher implements TokenMatcherInterface
{

    private $token;

    /**
     * @return Token
     * @throws Exception
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
        $symbol = $buffer->getSymbol();
        $attrList = [];
        if ($symbol >= 0x00 && $symbol <= 0x1F) {
            $type = TokenType::CTL_ASCII;
            goto valid_symbol;
        }
        if ($symbol >= 0x20 && $symbol <= 0x23) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol == 0x24) {
            $type = TokenType::DOLLAR;
            goto valid_symbol;
        }
        if ($symbol >= 0x25 && $symbol <= 0x27) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol == 0x28) {
            $type = TokenType::LEFT_BRACKET;
            goto valid_symbol;
        }
        if ($symbol == 0x29) {
            $type = TokenType::RIGHT_BRACKET;
            goto valid_symbol;
        }
        if ($symbol == 0x2A) {
            $type = TokenType::STAR;
            goto valid_symbol;
        }
        if ($symbol == 0x2B) {
            $type = TokenType::PLUS;
            goto valid_symbol;
        }
        if ($symbol == 0x2C) {
            $type = TokenType::COMMA;
            goto valid_symbol;
        }
        if ($symbol == 0x2D) {
            $type = TokenType::HYPHEN;
            goto valid_symbol;
        }
        if ($symbol == 0x2E) {
            $type = TokenType::DOT;
            goto valid_symbol;
        }
        if ($symbol == 0x2F) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol == 0x30) {
            $type = TokenType::DIGIT_ZERO;
            $attrList[TokenAttribute::DIGIT] = chr($symbol);
            goto valid_symbol;
        }
        if ($symbol >= 0x31 && $symbol <= 0x37) {
            $type = TokenType::DIGIT_OCT;
            $attrList[TokenAttribute::DIGIT] = chr($symbol);
            goto valid_symbol;
        }
        if ($symbol >= 0x38 && $symbol <= 0x39) {
            $type = TokenType::DIGIT_DEC;
            $attrList[TokenAttribute::DIGIT] = chr($symbol);
            goto valid_symbol;
        }
        if ($symbol >= 0x3A && $symbol <= 0x3E) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol == 0x3F) {
            $type = TokenType::QUESTION;
            goto valid_symbol;
        }
        if ($symbol == 0x40) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol >= 0x41 && $symbol <= 0x46) {
            $type = TokenType::OTHER_HEX_LETTER;
            $attrList[TokenAttribute::DIGIT] = chr($symbol);
            goto valid_symbol;
        }
        if ($symbol >= 0x47 && $symbol <= 0x4F) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($symbol == 0x50) {
            $type = TokenType::CAPITAL_P;
            goto valid_symbol;
        }
        if ($symbol >= 0x51 && $symbol <= 0x5A) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($symbol == 0x5B) {
            $type = TokenType::LEFT_SQUARE_BRACKET;
            goto valid_symbol;
        }
        if ($symbol == 0x5C) {
            $type = TokenType::BACKSLASH;
            goto valid_symbol;
        }
        if ($symbol == 0x5D) {
            $type = TokenType::RIGHT_SQUARE_BRACKET;
            goto valid_symbol;
        }
        if ($symbol == 0x5E) {
            $type = TokenType::CIRCUMFLEX;
            goto valid_symbol;
        }
        if ($symbol >= 0x5F && $symbol <= 0x60) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol >= 0x61 && $symbol <= 0x62) {
            $type = TokenType::OTHER_HEX_LETTER;
            $attrList[TokenAttribute::DIGIT] = strtoupper(chr($symbol));
            goto valid_symbol;
        }
        if ($symbol == 0x63) {
            $type = TokenType::SMALL_C;
            $attrList[TokenAttribute::DIGIT] = strtoupper(chr($symbol));
            goto valid_symbol;
        }
        if ($symbol >= 0x64 && $symbol <= 0x66) {
            $type = TokenType::OTHER_HEX_LETTER;
            $attrList[TokenAttribute::DIGIT] = strtoupper(chr($symbol));
            goto valid_symbol;
        }
        if ($symbol >= 0x67 && $symbol <= 0x6E) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($symbol == 0x6F) {
            $type = TokenType::SMALL_O;
            goto valid_symbol;
        }
        if ($symbol == 0x70) {
            $type = TokenType::SMALL_P;
            goto valid_symbol;
        }
        if ($symbol >= 0x71 && $symbol <= 0x74) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($symbol == 0x75) {
            $type = TokenType::SMALL_U;
            goto valid_symbol;
        }
        if ($symbol >= 0x76 && $symbol <= 0x77) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($symbol == 0x78) {
            $type = TokenType::SMALL_X;
            goto valid_symbol;
        }
        if ($symbol >= 0x79 && $symbol <= 0x7A) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($symbol == 0x7B) {
            $type = TokenType::LEFT_CURLY_BRACKET;
            goto valid_symbol;
        }
        if ($symbol == 0x7C) {
            $type = TokenType::VERTICAL_LINE;
            goto valid_symbol;
        }
        if ($symbol == 0x7D) {
            $type = TokenType::RIGHT_CURLY_BRACKET;
            goto valid_symbol;
        }
        if ($symbol == 0x7E) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($symbol == 0x7F) {
            $type = TokenType::OTHER_ASCII;
            goto valid_symbol;
        }
        if ($symbol >= 0x80 && $symbol <= 0x10FFFF) {
            $type = TokenType::NOT_ASCII;
            goto valid_symbol;
        } else {
            $type = TokenType::INVALID;
            goto invalid_symbol;
        }

        valid_symbol:
        invalid_symbol:
        $buffer->nextSymbol();
        $this->token = $tokenFactory->createToken($type);
        $this->token->setAttribute(TokenAttribute::CODE, $symbol);
        foreach ($attrList as $attrName => $attrValue) {
            $this->token->setAttribute($attrName, $attrValue);
        }
        return true;
    }
}
