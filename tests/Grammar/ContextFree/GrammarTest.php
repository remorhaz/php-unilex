<?php

namespace Remorhaz\UniLex\Test\Grammar\ContextFree;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception;
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

    /**
     * @throws Exception
     */
    public function testGetToken_ValueAdded_ReturnsSameValue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(3, 4);
        $actualValue = $grammar->getToken(3);
        self::assertSame(4, $actualValue);
    }

    /**
     * @throws Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 1 is not defined
     */
    public function testGetToken_SymbolNotExists_ThrowsException(): void
    {
        (new Grammar(1, 2))->getToken(1);
    }

    /**
     * @throws Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 1 is not defined as terminal
     */
    public function testGetToken_TokenNotExists_ThrowsException(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1);
        $grammar->getToken(1);
    }

    /**
     * @throws Exception
     */
    public function testGetEoiToken_TokenExists_ReturnMatchingValue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->getEoiToken();
        self::assertSame(3, $actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsTerminal_TokenExists_ReturnsTrue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->isTerminal(2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsTerminal_SymbolExists_ReturnsFalse(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1);
        $actualValue = $grammar->isTerminal(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 1 is not defined
     */
    public function testIsTerminal_SymbolNotExists_ThrowsException(): void
    {
        (new Grammar(1, 2))->isTerminal(1);
    }

    /**
     * @throws Exception
     */
    public function testIsEoiToken_EoiToken_ReturnsTrue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->isEoiToken(3);
        self::assertTrue($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsEoiToken_NotEoiToken_ReturnsFalse(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(1, 3);
        $grammar->addToken(2, 4);
        $actualValue = $grammar->isEoiToken(3);
        self::assertFalse($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsEoiSymbol_EoiSymbol_ReturnsTrue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->isEoiSymbol(2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsEoiSymbol_NotEoiSymbol_ReturnsFalse(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(1, 3);
        $grammar->addToken(2, 4);
        $actualValue = $grammar->isEoiSymbol(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testIsEoiSymbol_NotTerminal_ReturnsFalse(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1);
        $actualValue = $grammar->isEoiSymbol(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testTokenMatchesTerminal_MatchingToken_ReturnsTrue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->tokenMatchesTerminal(2, 3);
        self::assertTrue($actualValue);
    }

    /**
     * @throws Exception
     */
    public function testTokenMatchesTerminal_NotMatchingToken_ReturnsFalse(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(1, 3);
        $grammar->addToken(2, 4);
        $actualValue = $grammar->tokenMatchesTerminal(2, 3);
        self::assertFalse($actualValue);
    }

    /**
     * @throws Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Token 4 is not defined
     */
    public function testTokenMatchesTerminal_NotToken_ThrowsException(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $grammar->tokenMatchesTerminal(2, 4);
    }

    public function testGetTerminalList_NoTokensAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new Grammar(1, 2))->getTerminalList();
        self::assertSame([], $actualValue);
    }

    public function testGetTerminalList_TokensAdded_ReturnsSymbolArray(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(1, 2);
        $grammar->addToken(2, 3);
        $expectedValue = [1, 2];
        $actualValue = $grammar->getTerminalList();
        self::assertSame($expectedValue, $actualValue);
    }

    public function testGetNonTerminalList_NoProductionsAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new Grammar(1, 2))->getNonTerminalList();
        self::assertSame([], $actualValue);
    }

    public function testGetNonTerminalList_ProductionsAdded_ReturnsSymbolArray(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1, [3]);
        $grammar->addProduction(3, [2]);
        $expectedValue = [1, 3];
        $actualValue = $grammar->getNonTerminalList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 2 is terminal and can't have productions
     */
    public function testGetProductionList_Terminal_ThrowsException(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addToken(2, 3);
        $grammar->getProductionList(2);
    }

    /**
     * @throws Exception
     */
    public function testGetProductionList_NonTerminal_ReturnsMatchingValue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1, [3, 4]);
        $grammar->addProduction(1, []);
        $expectedValue = [[3, 4], []];
        $actualValue = $grammar->getProductionList(1);
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws Exception
     */
    public function testGetProduction_ProductionExists_ReturnsProduction(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1, [3, 4]);
        $grammar->addProduction(1, []);
        $expectedValue = [3, 4];
        $actualValue = $grammar->getProduction(1, 0);
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Symbol 1 has no production at index 1
     */
    public function testGetProduction_ProductionNotExists_ThrowsException(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1, [3, 4]);
        $grammar->getProduction(1, 1);
    }

    /**
     * @throws Exception
     */
    public function testGetFullProductionList_Created_ReturnsIterable(): void
    {
        $actualValue = (new Grammar(1, 2))->getFullProductionList();
        self::assertTrue(is_iterable($actualValue));
    }

    /**
     * @throws Exception
     */
    public function testGetFullProductionList_ProductionAdded_GeneratorReturnsMatchingValue(): void
    {
        $grammar = new Grammar(1, 2);
        $grammar->addProduction(1, [2]);
        $expectedValue = [1, 0, [2]];
        $actualValue = $grammar->getFullProductionList()->current();
        self::assertSame($expectedValue, $actualValue);
    }
}
