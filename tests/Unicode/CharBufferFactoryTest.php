<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\IO\StringBuffer;
use Remorhaz\UniLex\Unicode\CharBufferFactory;
use Remorhaz\UniLex\Unicode\Grammar\Utf8TokenMatcher;

#[CoversClass(CharBufferFactory::class)]
class CharBufferFactoryTest extends TestCase
{
    public function testCreateFromBuffer_BufferWithCharacter_ResultGetSymbolReturnsCharacterCode(): void
    {
        $source = new StringBuffer('本');
        $actualValue = CharBufferFactory::createFromBuffer($source, new Utf8TokenMatcher())->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }

    public function testCreateFromBuffer_BufferWithCharacterNoMatcher_ResultGetSymbolReturnsCharacterCode(): void
    {
        $source = new StringBuffer('本');
        $actualValue = CharBufferFactory::createFromBuffer($source)->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }

    public function testCreateFromString_StringWithCharacter_ResultGetSymbolReturnsCharacterCode(): void
    {
        $actualValue = CharBufferFactory::createFromString('本', new Utf8TokenMatcher())->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }

    public function testCreateFromString_StringWithCharacterNoMatcher_ResultGetSymbolReturnsCharacterCode(): void
    {
        $actualValue = CharBufferFactory::createFromString('本')->getSymbol();
        self::assertSame(0x672C, $actualValue);
    }
}
