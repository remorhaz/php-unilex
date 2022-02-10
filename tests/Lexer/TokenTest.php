<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Lexer;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
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
     * @throws UniLexException
     */
    public function testGetAttribute_AttributeNotSet_ThrowsException(): void
    {
        $token = new Token(1, false);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Token attribute \'a\' is not defined');
        $token->getAttribute('a');
    }

    /**
     * @throws UniLexException
     */
    public function testGetAttribute_AttributeSet_ReturnsSameValue(): void
    {
        $token = new Token(1, false);
        $token->setAttribute('a', 1);
        $actualValue = $token->getAttribute('a');
        self::assertSame(1, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testSetAttribute_AttributeSet_ThrowsException(): void
    {
        $token = new Token(1, false);
        $token->setAttribute('a', 1);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Token attribute \'a\' is already set');
        $token->setAttribute('a', 2);
    }
}
