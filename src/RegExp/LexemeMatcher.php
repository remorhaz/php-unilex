<?php

namespace Remorhaz\UniLex\RegExp;

use Remorhaz\UniLex\SymbolBufferInterface;

class LexemeMatcher
{

    public function match(SymbolBufferInterface $buffer, LexemeListenerInterface $listener): void
    {
        $symbol = null;
        $firstByte = $buffer->getSymbol();
        if ($firstByte >= 0x00 && $firstByte <= 0x1F) {
            $type = TokenType::CTL_ASCII;
            goto valid_symbol;
        }
        if ($firstByte >= 0x20 && $firstByte <= 0x23) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte == 0x24) {
            $type = TokenType::DOLLAR;
            goto valid_symbol;
        }
        if ($firstByte >= 0x25 && $firstByte <= 0x27) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte == 0x28) {
            $type = TokenType::LEFT_BRACKET;
            goto valid_symbol;
        }
        if ($firstByte == 0x29) {
            $type = TokenType::RIGHT_BRACKET;
            goto valid_symbol;
        }
        if ($firstByte == 0x2A) {
            $type = TokenType::STAR;
            goto valid_symbol;
        }
        if ($firstByte == 0x2B) {
            $type = TokenType::PLUS;
            goto valid_symbol;
        }
        if ($firstByte == 0x2C) {
            $type = TokenType::COMMA;
            goto valid_symbol;
        }
        if ($firstByte == 0x2D) {
            $type = TokenType::HYPHEN;
            goto valid_symbol;
        }
        if ($firstByte == 0x2E) {
            $type = TokenType::DOT;
            goto valid_symbol;
        }
        if ($firstByte == 0x2F) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte == 0x30) {
            $type = TokenType::DIGIT_ZERO;
            goto valid_symbol;
        }
        if ($firstByte >= 0x31 && $firstByte <= 0x37) {
            $type = TokenType::DIGIT_OCT;
            goto valid_symbol;
        }
        if ($firstByte >= 0x38 && $firstByte <= 0x39) {
            $type = TokenType::DIGIT_DEC;
            goto valid_symbol;
        }
        if ($firstByte >= 0x3A && $firstByte <= 0x3E) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte == 0x3F) {
            $type = TokenType::QUESTION;
            goto valid_symbol;
        }
        if ($firstByte == 0x40) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte >= 0x41 && $firstByte <= 0x46) {
            $type = TokenType::OTHER_HEX_LETTER;
            goto valid_symbol;
        }
        if ($firstByte >= 0x47 && $firstByte <= 0x4F) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x50) {
            $type = TokenType::CAPITAL_P;
            goto valid_symbol;
        }
        if ($firstByte >= 0x51 && $firstByte <= 0x5A) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x5B) {
            $type = TokenType::LEFT_SQUARE_BRACKET;
            goto valid_symbol;
        }
        if ($firstByte == 0x5C) {
            $type = TokenType::BACKSLASH;
            goto valid_symbol;
        }
        if ($firstByte == 0x5D) {
            $type = TokenType::RIGHT_SQUARE_BRACKET;
            goto valid_symbol;
        }
        if ($firstByte == 0x5E) {
            $type = TokenType::CIRCUMFLEX;
            goto valid_symbol;
        }
        if ($firstByte >= 0x5F && $firstByte <= 0x60) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte >= 0x61 && $firstByte <= 0x62) {
            $type = TokenType::OTHER_HEX_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x63) {
            $type = TokenType::SMALL_C;
            goto valid_symbol;
        }
        if ($firstByte >= 0x64 && $firstByte <= 0x66) {
            $type = TokenType::OTHER_HEX_LETTER;
            goto valid_symbol;
        }
        if ($firstByte >= 0x67 && $firstByte <= 0x6E) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x6F) {
            $type = TokenType::SMALL_O;
            goto valid_symbol;
        }
        if ($firstByte == 0x70) {
            $type = TokenType::SMALL_P;
            goto valid_symbol;
        }
        if ($firstByte >= 0x72 && $firstByte <= 0x74) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x75) {
            $type = TokenType::SMALL_U;
            goto valid_symbol;
        }
        if ($firstByte >= 0x76 && $firstByte <= 0x77) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x78) {
            $type = TokenType::SMALL_X;
            goto valid_symbol;
        }
        if ($firstByte >= 0x79 && $firstByte <= 0x7A) {
            $type = TokenType::OTHER_ASCII_LETTER;
            goto valid_symbol;
        }
        if ($firstByte == 0x7B) {
            $type = TokenType::LEFT_CURLY_BRACKET;
            goto valid_symbol;
        }
        if ($firstByte == 0x7C) {
            $type = TokenType::VERTICAL_LINE;
            goto valid_symbol;
        }
        if ($firstByte == 0x7D) {
            $type = TokenType::RIGHT_CURLY_BRACKET;
            goto valid_symbol;
        }
        if ($firstByte == 0x7E) {
            $type = TokenType::PRINTABLE_ASCII_OTHER;
            goto valid_symbol;
        }
        if ($firstByte == 0x7F) {
            $type = TokenType::OTHER_ASCII;
            goto valid_symbol;
        }
        if ($firstByte >= 0x80 && $firstByte <= 0x10FFFF) {
            $type = TokenType::NOT_ASCII;
            goto valid_symbol;
        } else {
            $type = TokenType::INVALID;
            goto invalid_symbol;
        }

        valid_symbol:
        $buffer->nextSymbol();
        $lexeme = new Lexeme($buffer->getLexemeInfo(), $type, $symbol);
        $listener->onValidSymbol($lexeme);
        goto finish;

        invalid_symbol:
        $buffer->nextSymbol();
        $lexeme = new Lexeme($buffer->getLexemeInfo(), $type, $symbol);
        $listener->onInvalidSymbol($lexeme);
        goto finish;

        finish:
    }
}
