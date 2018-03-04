<?php

namespace Remorhaz\UniLex\Unicode;

use Remorhaz\UniLex\LexemeBuffer;
use Remorhaz\UniLex\LexemeMatcherInterface;
use Remorhaz\UniLex\LexemeReader;
use Remorhaz\UniLex\SymbolBuffer;
use Remorhaz\UniLex\SymbolBufferInterface;
use Remorhaz\UniLex\Unicode\Grammar\LexemeFactory;

abstract class BufferFactory
{

    public static function createFromBuffer(
        SymbolBufferInterface $source,
        LexemeMatcherInterface $matcher
    ): SymbolBufferInterface {
        $reader = new LexemeReader($source, $matcher, new LexemeFactory());
        return new LexemeBuffer($reader, new CodeSymbolFactory);
    }

    public static function createFromString(string $text, LexemeMatcherInterface $matcher): SymbolBufferInterface
    {
        $source = SymbolBuffer::fromString($text);
        return self::createFromBuffer($source, $matcher);
    }

    public static function createFromUtf8String(string $text): SymbolBufferInterface
    {
        return self::createFromString($text, new Utf8LexemeMatcher);
    }
}
