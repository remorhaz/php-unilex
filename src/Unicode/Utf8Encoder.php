<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Unicode;

class Utf8Encoder
{
    public function encode(int ...$symbolList): string
    {
        $buffer = '';
        foreach ($symbolList as $symbol) {
            if (0x00 <= $symbol && $symbol <= 0x7F) {
                $buffer .= chr($symbol);
                continue;
            }
            if (0x80 <= $symbol && $symbol <= 0x07FF) {
                $buffer .=
                    chr(0xC0 | ($symbol >> 0x06)) .
                    chr(0x80 | ($symbol & 0x3F));
                continue;
            }
            if (0x0800 <= $symbol && $symbol <= 0xFFFF) {
                $buffer .=
                    chr(0xE0 | ($symbol >> 0x0C)) .
                    chr(0x80 | (($symbol >> 0x06) & 0x3F)) .
                    chr(0x80 | ($symbol & 0x3F));
                continue;
            }
            if (0x010000 <= $symbol && $symbol <= 0x10FFFF) {
                $buffer .=
                    chr(0xF0 | ($symbol >> 0x12)) .
                    chr(0x80 | (($symbol >> 0x0C) & 0x3F)) .
                    chr(0x80 | (($symbol >> 0x06) & 0x3F)) .
                    chr(0x80 | ($symbol & 0x3F));
                continue;
            }
            $buffer .= '�';
        }

        return $buffer;
    }
}
