<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Token;

/**
 * @covers \Remorhaz\UniLex\Token
 */
class TokenTest extends TestCase
{

    public function testGetType(): void
    {
        $actualValue = (new Token(1, false))->getType();
        self::assertSame(1, $actualValue);
    }
}