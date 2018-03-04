<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexeme;
use Remorhaz\UniLex\LexemeFactoryInterface;
use Remorhaz\UniLex\SymbolBufferInterface;

class Utf8LexemeMatcher implements LexemeMatcherInterface
{

    public function match(SymbolBufferInterface $buffer, LexemeFactoryInterface $lexemeFactory): Lexeme
    {
        $symbol = null;
        $firstByte = $buffer->getSymbol();
        if ($firstByte >= 0 && $firstByte <= 0x7F) { // 1-byte symbol
            $symbolInfo = new SymbolInfo($firstByte);
            $buffer->nextSymbol();
            $lexeme = new SymbolLexeme($buffer->getLexemeInfo(), $symbolInfo->getCode());
            $lexeme->setMatcherInfo($symbolInfo);
            return $lexeme;
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
            $symbolInfo = new SymbolInfo($symbol);
            $buffer->nextSymbol();
            $lexeme = new SymbolLexeme($buffer->getLexemeInfo(), $symbolInfo->getCode());
            $lexeme->setMatcherInfo($symbolInfo);
            return $lexeme;
        }
        goto invalid_byte;

        invalid_byte:
        $lastInvalidByte = $buffer->getSymbol();
        $buffer->nextSymbol();
        return new InvalidBytesLexeme($buffer->getLexemeInfo(), $lastInvalidByte);
    }
}
