<?php

namespace Remorhaz\UniLex\Test\Unicode\Grammar;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Unicode\Grammar\TokenFactory;
use Remorhaz\UniLex\Unicode\Grammar\TokenType;

/**
 * @covers \Remorhaz\UniLex\Unicode\Grammar\TokenFactory
 */
class TokenFactoryTest extends TestCase
{

    public function testCreateEoiToken_Constructed_ResultIsEoiReturnsTrue(): void
    {
        $actualValue = (new TokenFactory)
            ->createEoiToken()
            ->isEoi();
        self::assertTrue($actualValue);
    }

    public function testCreateToken_EoiId_ResultIsEoiReturnsTrue(): void
    {
        $actualValue = (new TokenFactory)
            ->createToken(TokenType::EOI)
            ->isEoi();
        self::assertTrue($actualValue);
    }

    public function testCreateToken_NotEoiId_ResultIsEoiReturnsTrue(): void
    {
        $actualValue = (new TokenFactory)
            ->createToken(TokenType::SYMBOL)
            ->isEoi();
        self::assertFalse($actualValue);
    }
}
