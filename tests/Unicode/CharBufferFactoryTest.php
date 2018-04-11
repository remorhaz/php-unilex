<?php

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\CharBuffer;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

/**
 * @covers \Remorhaz\UniLex\Unicode\CharBufferFactory
 */
class CharBufferFactoryTest extends TestCase
{

    public function testCreateFromBuffer_CreateFromBuffer_ResultGetSymbolReturnsUnicodeCharacter(): void
    {
        $source = CharBuffer::fromString('本');
        $actualValue = CharBufferFactory::createFromBuffer($source, new Utf8TokenMatcher)->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }

    public function testCreateFromString_CreateFromBuffer_ResultGetSymbolReturnsUnicodeCharacter(): void
    {
        $actualValue = CharBufferFactory::createFromString('本', new Utf8TokenMatcher)->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }

    public function testCreateFromUtf8String_CreateFromBuffer_ResultGetSymbolReturnsUnicodeCharacter(): void
    {
        $actualValue = CharBufferFactory::createFromUtf8String('本')->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }
}
