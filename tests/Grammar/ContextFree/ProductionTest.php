<?php

namespace Remorhaz\UniLex\Test\Grammar\ContextFree;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\Production;

/**
 * @covers \Remorhaz\UniLex\Grammar\ContextFree\Production
 */
class ProductionTest extends TestCase
{

    public function testCastToString_Constructed_ReturnsMatchingString(): void
    {
        $production = new Production(1, 2, 3);
        $actualValue = (string) $production;
        self::assertSame("1:2", $actualValue);
    }

    public function testGetSymbolId_ConstructWithValue_ReturnsSameValue(): void
    {
        $production = new Production(1, 2, 3);
        $actualValue = $production->getSymbolId();
        self::assertSame(1, $actualValue);
    }

    public function testGetIndex_ConstructWithValue_ReturnsSameValue(): void
    {
        $production = new Production(1, 2, 3);
        $actualValue = $production->getIndex();
        self::assertSame(2, $actualValue);
    }

    public function testGetSymbolList_ConstructWithOutValue_ReturnsEmptyArray(): void
    {
        $production = new Production(1, 2);
        $actualValue = $production->getSymbolList();
        self::assertSame([], $actualValue);
    }

    public function testGetSymbolList_ConstructWithValues_ReturnsMatchingArray(): void
    {
        $production = new Production(1, 2, 3, 4);
        $expectedValue = [3, 4];
        $actualValue = $production->getSymbolList();
        self::assertSame($expectedValue, $actualValue);
    }

    public function testIsEpsilon_EmptySymbolList_ReturnsTrue(): void
    {
        $actualValue = (new Production(1, 2))->isEpsilon();
        self::assertTrue($actualValue);
    }

    public function testIsEpsilon_NotEmptySymbolList_ReturnsTrue(): void
    {
        $actualValue = (new Production(1, 2, 3))->isEpsilon();
        self::assertFalse($actualValue);
    }
}
