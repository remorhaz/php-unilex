<?php

namespace Remorhaz\UniLex\Test\Grammar\SDD;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Grammar\ContextFree\Production;
use Remorhaz\UniLex\Grammar\SDD\ContextFactoryInterface;
use Remorhaz\UniLex\Grammar\SDD\ProductionContextInterface;
use Remorhaz\UniLex\Grammar\SDD\SymbolContextInterface;
use Remorhaz\UniLex\Grammar\SDD\TokenContextInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationScheme;
use Remorhaz\UniLex\Parser\ParsedProduction;
use Remorhaz\UniLex\Parser\ParsedSymbol;
use Remorhaz\UniLex\Parser\ParsedToken;
use Remorhaz\UniLex\Token;

/**
 * @covers \Remorhaz\UniLex\Grammar\SDD\TranslationScheme
 */
class TranslationSchemeTest extends TestCase
{

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Action for symbol 2:0[0]->a is already set
     */
    public function testAddSymbolAction_SymbolActionExists_ThrowsException(): void
    {
        $scheme = new TranslationScheme($this->createContextFactory());
        $production = new Production(2, 0, 3);
        $action = function () {
        };
        $scheme->addSymbolAction($production, 0, 'a', $action);
        $scheme->addSymbolAction($production, 0, 'a', $action);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Action for production 2:0->a is already set
     */
    public function testAddProductionAction_ProductionActionExists_ThrowsException(): void
    {
        $scheme = new TranslationScheme($this->createContextFactory());
        $production = new Production(2, 0, 3);
        $action = function () {
        };
        $scheme->addProductionAction($production, 'a', $action);
        $scheme->addProductionAction($production, 'a', $action);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     * @expectedException \Remorhaz\UniLex\Exception
     * @expectedExceptionMessage Action for terminal symbol 1->a is already set
     */
    public function testAddTokenAction_TokenActionExists_ThrowsException(): void
    {
        $scheme = new TranslationScheme($this->createContextFactory());
        $action = function () {
        };
        $scheme->addTokenAction(1, 'a', $action);
        $scheme->addTokenAction(1, 'a', $action);
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testApplySymbolActions_SymbolActionAdded_SymbolAttributeSet(): void
    {
        $scheme = new TranslationScheme($this->createContextFactory());
        $production = new Production(2, 0, 3);
        $action = function (): string {
            return 'b';
        };
        $scheme->addSymbolAction($production, 0, 'a', $action);
        $header = new ParsedSymbol(1, 2);
        $symbol = new ParsedSymbol(2, 3);
        $parsedProduction = new ParsedProduction($header, 0, $symbol);
        $scheme->applySymbolActions($parsedProduction, 0);
        self::assertSame('b', $symbol->getAttribute('a'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testApplyProductionActions_ProductionActionAdded_HeaderAttributeSet(): void
    {
        $scheme = new TranslationScheme($this->createContextFactory());
        $production = new Production(2, 0, 3);
        $action = function (): string {
            return 'b';
        };
        $scheme->addProductionAction($production, 'a', $action);
        $header = new ParsedSymbol(1, 2);
        $symbol = new ParsedSymbol(2, 3);
        $parsedProduction = new ParsedProduction($header, 0, $symbol);
        $scheme->applyProductionActions($parsedProduction);
        self::assertSame('b', $header->getAttribute('a'));
    }

    /**
     * @throws \Remorhaz\UniLex\Exception
     */
    public function testApplyTokenActions_TokenActionAdded_SymbolAttributeSet(): void
    {
        $scheme = new TranslationScheme($this->createContextFactory());
        $action = function (): string {
            return 'b';
        };
        $scheme->addTokenAction(2, 'a', $action);
        $symbol = new ParsedSymbol(1, 2);
        $token = new ParsedToken(2, new Token(3, false));
        $scheme->applyTokenActions($symbol, $token);
        self::assertSame('b', $symbol->getAttribute('a'));
    }

    private function createContextFactory(): ContextFactoryInterface
    {
        return new class implements ContextFactoryInterface
        {
            public function createProductionContext(ParsedProduction $production): ProductionContextInterface
            {
                return new class implements ProductionContextInterface
                {
                };
            }

            public function createSymbolContext(ParsedProduction $production, int $symbolIndex): SymbolContextInterface
            {
                return new class implements SymbolContextInterface
                {
                };
            }

            public function createTokenContext(ParsedSymbol $symbol, ParsedToken $token): TokenContextInterface
            {
                return new class implements TokenContextInterface
                {
                };
            }
        };
    }
}
