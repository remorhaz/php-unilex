<?php

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexer\Token;

/**
 * @covers \Remorhaz\UniLex\Lexer\Token
 */
class TokenTest extends TestCase
{

    public function testGetType_ConstructedWithValue_ReturnsSameValue(): void
    {
        $actualValue = (new Token(1, false))->getType();
        self::assertSame(1, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Token attribute 'a' is not defined
     */
    public function testGetAttribute_AttributeNotSet_ThrowsException(): void
    {
        (new Token(1, false))->getAttribute('a');
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testGetAttribute_AttributeSet_ReturnsSameValue(): void
    {
        $token = new Token(1, false);
        $token->setAttribute('a', 1);
        $actualValue = $token->getAttribute('a');
        self::assertSame(1, $actualValue);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Token attribute 'a' is already set
     */
    public function testSetAttribute_AttributeSet_ThrowsException(): void
    {
        $token = new Token(1, false);
        $token->setAttribute('a', 1);
        $token->setAttribute('a', 2);
    }
}
