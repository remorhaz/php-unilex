<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\Lexer\TokenBuffer;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\IO\CharBuffer;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

abstract class CharBufferFactory
{

    public static function createFromBuffer(
        CharBufferInterface $source,
        TokenMatcherInterface $matcher
    ): CharBufferInterface {
        $reader = new TokenReader($source, $matcher, new TokenFactory());
        return new TokenBuffer($reader, new CharFactory);
    }

    public static function createFromString(string $text, TokenMatcherInterface $matcher): CharBufferInterface
    {
        $source = CharBuffer::fromString($text);
        return self::createFromBuffer($source, $matcher);
    }

    public static function createFromUtf8String(string $text): CharBufferInterface
    {
        return self::createFromString($text, new Utf8TokenMatcher);
    }
}
