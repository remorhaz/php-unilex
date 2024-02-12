<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\TokenSpec;

#[CoversClass(TokenSpec::class)]
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
