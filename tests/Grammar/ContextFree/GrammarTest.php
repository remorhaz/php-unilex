<?php

namespace Remorhaz\UniLex\Test\Grammar\ContextFree;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;

class GrammarTest extends TestCase
{

    public function testGetStartSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new Grammar(1, 2);
        $actualValue = $grammar->getStartSymbol();
        self::assertEquals(1, $actualValue);
    }

    public function testGetEoiSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new Grammar(1, 2);
        $actualValue = $grammar->getEoiSymbol();
        self::assertEquals(2, $actualValue);
    }
}
