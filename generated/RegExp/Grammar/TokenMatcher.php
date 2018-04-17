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
        $context = $this->createContext($buffer, $tokenFactory);
        goto state1;

        state1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x1F) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::CTL_ASCII)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
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
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::PRINTABLE_ASCII_OTHER)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x24 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DOLLAR)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x28 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::LEFT_BRACKET)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x29 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::RIGHT_BRACKET)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2A == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::STAR)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2B == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::PLUS)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::COMMA)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2D == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::HYPHEN)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x2E == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DOT)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DIGIT_ZERO)
                ->setTokenAttribute(TokenAttribute::CODE, $char)
                ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x31 <= $char && $char <= 0x37) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DIGIT_OCT)
                ->setTokenAttribute(TokenAttribute::CODE, $char)
                ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x38 == $char || 0x39 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DIGIT_DEC)
                ->setTokenAttribute(TokenAttribute::CODE, $char)
                ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x3F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::QUESTION)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x41 <= $char && $char <= 0x46 || 0x61 == $char || 0x62 == $char || 0x64 <= $char && $char <= 0x66) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::OTHER_HEX_LETTER)
                ->setTokenAttribute(TokenAttribute::CODE, $char)
                ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));
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
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::OTHER_ASCII_LETTER)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x50 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::CAPITAL_P)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5B == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::LEFT_SQUARE_BRACKET)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5D == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::RIGHT_SQUARE_BRACKET)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x5E == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::CIRCUMFLEX)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x63 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SMALL_C)
                ->setTokenAttribute(TokenAttribute::CODE, $char)
                ->setTokenAttribute(TokenAttribute::DIGIT, chr($char));
            return true;
        }
        if (0x6F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SMALL_O)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x70 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SMALL_P)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x75 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SMALL_U)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x78 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SMALL_X)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7B == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::LEFT_CURLY_BRACKET)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::VERTICAL_LINE)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7D == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::RIGHT_CURLY_BRACKET)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x7F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::OTHER_ASCII)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        if (0x80 <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::NOT_ASCII)
                ->setTokenAttribute(TokenAttribute::CODE, $char);
            return true;
        }
        goto error;

        error:
        if ($context->getBuffer()->isEnd()) {
            return false;
        }
        $char = $context->getBuffer()->getSymbol();
        $context->getBuffer()->nextSymbol();
        $context
            ->setNewToken(TokenType::INVALID)
            ->setTokenAttribute(TokenAttribute::CODE, $char);
        return true;
    }
}
