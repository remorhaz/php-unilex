<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\IO\StringBuffer;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\IO\CharBufferInterface;

abstract class CharBufferFactory
{

    public static function createFromBuffer(
        CharBufferInterface $source,
        TokenMatcherInterface $matcher = null
    ): CharBufferInterface {
        $buffer = new CharBuffer($source);
        if (isset($matcher)) {
            $buffer->setMatcher($matcher);
        }
        return $buffer;
    }

    public static function createFromString(string $text, TokenMatcherInterface $matcher = null): CharBufferInterface
    {
        $source = new StringBuffer($text);
        return self::createFromBuffer($source, $matcher);
    }
}
