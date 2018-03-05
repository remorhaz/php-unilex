<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\TokenBuffer;
use Remorhaz\UniLex\TokenMatcherInterface;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\CharBufferInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;

abstract class CharBufferFactory
{

    public static function createFromBuffer(
        CharBufferInterface $source,
        TokenMatcherInterface $matcher
    ): CharBufferInterface {
        $reader = new TokenReader($source, $matcher, new TokenFactory());
        return new TokenBuffer($reader, new CodeSymbolFactory);
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
