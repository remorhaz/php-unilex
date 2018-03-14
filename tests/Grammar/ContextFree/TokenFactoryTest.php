<?php

namespace Remorhaz\UniLex\Test\Grammar\ContextFree;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;
use Remorhaz\UniLex\Grammar\ContextFree\TokenFactory;

/**
 * @covers \Remorhaz\UniLex\Grammar\ContextFree\TokenFactory
 */
class TokenFactoryTest extends TestCase
{

    public function testCreateEoiToken_EoiTokenAddedToGrammar_TokenIsEoiReturnsTrue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = (new TokenFactory($grammar))
            ->createEoiToken()
            ->isEoi();
        self::assertTrue($actualValue);
    }

    public function testCreateEoiToken_EoiTokenAddedToGrammar_TokenGetTypeReturnsMatchingType(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = (new TokenFactory($grammar))
            ->createEoiToken()
            ->getType();
        self::assertSame(3, $actualValue);
    }

    public function testCreateToken_ValidType_TokenGetTypeReturnsSameType(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $grammar->addToken(3, 4);
        $actualValue = (new TokenFactory($grammar))
            ->createToken(4)
            ->getType();
        self::assertSame(4, $actualValue);
    }

    public function testCreateToken_NotEoiTokenType_TokenIsEoiReturnsFalse(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $grammar->addToken(3, 4);
        $actualValue = (new TokenFactory($grammar))
            ->createToken(4)
            ->isEoi();
        self::assertFalse($actualValue);
    }

    public function testCreateToken_EoiTokenType_TokenIsEoiReturnsTrue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = (new TokenFactory($grammar))
            ->createToken(3)
            ->isEoi();
        self::assertTrue($actualValue);
    }
}
