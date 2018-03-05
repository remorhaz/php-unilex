<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\TokenBuffer;
use Remorhaz\UniLex\TokenMatcherInterface;
use Remorhaz\UniLex\TokenReader;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferInterface;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;

abstract class BufferFactory
{

    public static function createFromBuffer(
        SymbolBufferInterface $source,
        TokenMatcherInterface $matcher
    ): SymbolBufferInterface {
        $reader = new TokenReader($source, $matcher, new TokenFactory());
        return new TokenBuffer($reader, new CodeSymbolFactory);
    }

    public static function createFromString(string $text, TokenMatcherInterface $matcher): SymbolBufferInterface
    {
        $source = SymbolBuffer::fromString($text);
        return self::createFromBuffer($source, $matcher);
    }

    public static function createFromUtf8String(string $text): SymbolBufferInterface
    {
        return self::createFromString($text, new Utf8TokenMatcher);
    }
}
