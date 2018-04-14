<?php

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\TokenSpec;

class TokenSpecTest extends TestCase
{

    public function testGetRegExp_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new TokenSpec("a", 1, ""))->getRegExp();
        self::assertSame("a", $actualValue);
    }

    public function testGetTokenType_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new TokenSpec("a", 1, ""))->getTokenType();
        self::assertSame(1, $actualValue);
    }

    public function testGetCode_ConstructWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new TokenSpec("a", 1, "//"))->getCode();
        self::assertSame("//", $actualValue);
    }
}
