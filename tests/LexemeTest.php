<?php

namespace Remorhaz\UniLex\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Lexeme;

/**
 * @covers \Remorhaz\UniLex\Lexeme
 */
class LexemeTest extends TestCase
{

    public function testGetType(): void
    {
        $actualValue = (new Lexeme(1, false))->getType();
        self::assertSame(1, $actualValue);
    }
}
