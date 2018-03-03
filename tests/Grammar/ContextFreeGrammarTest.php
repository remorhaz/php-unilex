<?php

namespace Remorhaz\UniLex\Test\Grammar;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFreeGrammar;

class ContextFreeGrammarTest extends TestCase
{

    public function testGetStartSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new ContextFreeGrammar(1, 2);
        $actualValue = $grammar->getStartSymbol();
        self::assertEquals(1, $actualValue);
    }

    public function testGetEofSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new ContextFreeGrammar(1, 2);
        $actualValue = $grammar->getEoiSymbol();
        self::assertEquals(2, $actualValue);
    }
}
