<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\TokenSpec;

/**
 * @covers \Remorhaz\UniLex\Lexer\TokenSpec
 */
class TokenSpecTest extends TestCase
{
    public function testGetRegExp_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new TokenSpec("a", ""))->getRegExp();
        self::assertSame("a", $actualValue);
    }

    public function testGetCode_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new TokenSpec("a", "//"))->getCode();
        self::assertSame("//", $actualValue);
    }
}
