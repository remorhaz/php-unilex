<?php

namespace Remorhaz\UniLex\Test\Unicode;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Token;
use Remorhaz\UniLex\Unicode\CharFactory;
use Remorhaz\UniLex\Unicode\Grammar\TokenAttribute;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

/**
 * @covers \Remorhaz\UniLex\Unicode\CharFactory
 */
class CharFactoryTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetChar_DefaultUnicodeCharAttribute_ReturnsAttributeValue(): void
    {
        $token = new Token(TokenType::SYMBOL, false);
        $token->setAttribute(TokenAttribute::UNICODE_CHAR, 0x61);
        $actualValue = (new CharFactory)->getChar($token);
        self::assertSame(0x61, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetChar_CustomUnicodeCharAttribute_ReturnsAttributeValue(): void
    {
        $token = new Token(TokenType::SYMBOL, false);
        $token->setAttribute('foo', 0x61);
        $actualValue = (new CharFactory('foo'))->getChar($token);
        self::assertSame(0x61, $actualValue);
    }
}
