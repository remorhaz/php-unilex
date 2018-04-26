<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\IO\StringBuffer;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

abstract class CharBufferFactory
{

    public static function createFromBuffer(
        CharBufferInterface $source,
        TokenMatcherInterface $matcher
    ): CharBufferInterface {
        $buffer = new CharBuffer($source);
        $buffer->setMatcher($matcher);
        return $buffer;
    }

    public static function createFromString(string $text, TokenMatcherInterface $matcher): CharBufferInterface
    {
        $source = new StringBuffer($text);
        return self::createFromBuffer($source, $matcher);
    }

    public static function createFromUtf8String(string $text): CharBufferInterface
    {
        return self::createFromString($text, new Utf8TokenMatcher);
    }
}
