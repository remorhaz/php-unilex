<?php

namespace Remorhaz\UniLex\Test\LL1Parser\Lookup;

use PHPUnit\Framework\TestCase;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\ConfigFile;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\SymbolType;
use Remorhaz\UniLex\Example\SimpleExpr\Grammar\TokenType;
use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;
use Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder;

/**
 * @covers \Remorhaz\UniLex\LL1Parser\Lookup\TableBuilder
 */
class TableBuilderTest extends TestCase
{

    /**
     * @param string $configFile
     * @param array $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidGrammarTables
     */
    public function testGetFirst_ValidGrammar_ResultGetTokensReturnsMatchingValue(
        string $configFile,
        array $expectedValue
    ): void {
        $grammar = GrammarLoader::loadFile($configFile);
        $actualValue = (new TableBuilder($grammar))->getTable()->exportMap();
        self::assertEquals($expectedValue, $actualValue);
    }

    public function providerValidGrammarTables(): array
    {
        return [
            "Grammar from SimpleExpr example" => [
                ConfigFile::getPath(),
                $this->getSimpleExprGrammarTable(),
            ],
        ];
    }

    private function getSimpleExprGrammarTable(): array
    {
        return [
            SymbolType::NT_E0 => [
                TokenType::L_PARENTHESIS => [SymbolType::NT_T0, SymbolType::NT_E1],
                TokenType::ID => [SymbolType::NT_T0, SymbolType::NT_E1],
            ],
            SymbolType::NT_E1 => [
                TokenType::PLUS => [SymbolType::T_PLUS, SymbolType::NT_T0, SymbolType::NT_E1],
                TokenType::R_PARENTHESIS => [],
                TokenType::EOI => [],
            ],
            SymbolType::NT_T0 => [
                TokenType::L_PARENTHESIS => [SymbolType::NT_F, SymbolType::NT_T1],
                TokenType::ID => [SymbolType::NT_F, SymbolType::NT_T1],
            ],
            SymbolType::NT_T1 => [
                TokenType::PLUS => [],
                TokenType::STAR => [SymbolType::T_STAR, SymbolType::NT_F, SymbolType::NT_T1],
                TokenType::R_PARENTHESIS => [],
                TokenType::EOI => [],
            ],
            SymbolType::NT_F => [
                TokenType::L_PARENTHESIS => [
                    SymbolType::T_L_PARENTHESIS,
                    SymbolType::NT_E0,
                    SymbolType::T_R_PARENTHESIS,
                ],
                TokenType::ID => [SymbolType::T_ID],
            ],
        ];
    }
}
