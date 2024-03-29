<?php

declare(strict_types=1);

namespace Remorhaz\UniLex\Test\Grammar\ContextFree;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Grammar\ContextFree\Grammar;

#[CoversClass(Grammar::class)]
class GrammarTest extends TestCase
{
    public function testGetRootSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $actualValue = $grammar->getRootSymbol();
        self::assertEquals(0, $actualValue);
    }

    public function testGetStartSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $actualValue = $grammar->getStartSymbol();
        self::assertEquals(1, $actualValue);
    }

    public function testGetEoiSymbol_ConstructWithValue_ReturnsSameValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $actualValue = $grammar->getEoiSymbol();
        self::assertEquals(2, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetToken_ValueAdded_ReturnsSameValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(3, 4);
        $actualValue = $grammar->getToken(3);
        self::assertSame(4, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetToken_SymbolNotExists_ThrowsException(): void
    {
        $grammar = new Grammar(0, 1, 2);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 1 is not defined');
        $grammar->getToken(1);
    }

    /**
     * @throws UniLexException
     */
    public function testGetToken_TokenNotExists_ThrowsException(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 1 is not defined as terminal');
        $grammar->getToken(1);
    }

    /**
     * @throws UniLexException
     */
    public function testGetEoiToken_TokenExists_ReturnMatchingValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->getEoiToken();
        self::assertSame(3, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsTerminal_TokenExists_ReturnsTrue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->isTerminal(2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsTerminal_SymbolExists_ReturnsFalse(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1);
        $actualValue = $grammar->isTerminal(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsTerminal_SymbolNotExists_ThrowsException(): void
    {
        $grammar = new Grammar(0, 1, 2);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 1 is not defined');
        $grammar->isTerminal(1);
    }

    /**
     * @throws UniLexException
     */
    public function testIsEoiToken_EoiToken_ReturnsTrue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->isEoiToken(3);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsEoiToken_NotEoiToken_ReturnsFalse(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 3);
        $grammar->addToken(2, 4);
        $actualValue = $grammar->isEoiToken(3);
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsEoiSymbol_EoiSymbol_ReturnsTrue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->isEoiSymbol(2);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsEoiSymbol_NotEoiSymbol_ReturnsFalse(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 3);
        $grammar->addToken(2, 4);
        $actualValue = $grammar->isEoiSymbol(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testIsEoiSymbol_NotTerminal_ReturnsFalse(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1);
        $actualValue = $grammar->isEoiSymbol(1);
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testTokenMatchesTerminal_MatchingToken_ReturnsTrue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);
        $actualValue = $grammar->tokenMatchesTerminal(2, 3);
        self::assertTrue($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testTokenMatchesTerminal_NotMatchingToken_ReturnsFalse(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 3);
        $grammar->addToken(2, 4);
        $actualValue = $grammar->tokenMatchesTerminal(2, 3);
        self::assertFalse($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testTokenMatchesTerminal_NotToken_ThrowsException(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Token 4 is not defined');
        $grammar->tokenMatchesTerminal(2, 4);
    }

    public function testGetTerminalList_NoTokensAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new Grammar(0, 1, 2))->getTerminalList();
        self::assertSame([], $actualValue);
    }

    public function testGetTerminalList_TokensAdded_ReturnsSymbolArray(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(1, 2);
        $grammar->addToken(2, 3);
        $expectedValue = [1, 2];
        $actualValue = $grammar->getTerminalList();
        self::assertSame($expectedValue, $actualValue);
    }

    public function testGetNonTerminalList_NoProductionsAdded_ReturnsEmptyArray(): void
    {
        $actualValue = (new Grammar(0, 1, 2))->getNonTerminalList();
        self::assertSame([], $actualValue);
    }

    public function testGetNonTerminalList_ProductionsAdded_ReturnsSymbolArray(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1, 3);
        $grammar->addProduction(3, 2);
        $expectedValue = [1, 3];
        $actualValue = $grammar->getNonTerminalList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetProductionList_Terminal_ThrowsException(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addToken(2, 3);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 2 is terminal and can\'t have productions');
        $grammar->getProductionList(2);
    }

    /**
     * @throws UniLexException
     */
    public function testGetProductionList_NonTerminal_ReturnsMatchingValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1, 3, 4);
        $grammar->addProduction(1);
        $expectedValue = [[3, 4], []];
        $productionList = $grammar->getProductionList(1);
        self::assertCount(2, $productionList);
        foreach ($expectedValue as $productionIndex => $tokenList) {
            self::assertArrayHasKey($productionIndex, $productionList);
            $production = $productionList[$productionIndex];
            self::assertSame(1, $production->getHeaderId());
            self::assertSame($productionIndex, $production->getIndex());
            self::assertSame($tokenList, $production->getSymbolList());
        }
    }

    /**
     * @throws UniLexException
     */
    public function testGetProduction_ProductionExists_ReturnsProduction(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1, 3, 4);
        $grammar->addProduction(1);
        $expectedValue = [3, 4];
        $actualValue = $grammar->getProduction(1, 0)->getSymbolList();
        self::assertSame($expectedValue, $actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetProduction_ProductionNotExists_ThrowsException(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1, 3, 4);

        $this->expectException(UniLexException::class);
        $this->expectExceptionMessage('Symbol 1 has no production at index 1');
        $grammar->getProduction(1, 1);
    }

    /**
     * @throws UniLexException
     */
    public function testGetProduction_RootSymbol_ReturnsRootProduction(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1, 3);
        $production = $grammar->getProduction(0, 0);
        self::assertSame(0, $production->getHeaderId());
        self::assertSame(0, $production->getIndex());
        self::assertSame([1, 2], $production->getSymbolList());
    }

    /**
     * @throws UniLexException
     */
    public function testGetFullProductionList_Created_ReturnsArray(): void
    {
        $actualValue = (new Grammar(0, 1, 2))->getFullProductionList();
        self::assertIsArray($actualValue);
    }

    /**
     * @throws UniLexException
     */
    public function testGetFullProductionList_ProductionAdded_ReturnsMatchingValue(): void
    {
        $grammar = new Grammar(0, 1, 2);
        $grammar->addProduction(1, 2);
        foreach ($grammar->getFullProductionList() as $production) {
            self::assertSame(1, $production->getHeaderId());
            self::assertSame(0, $production->getIndex());
            self::assertSame([2], $production->getSymbolList());
            break;
        }
    }
}
